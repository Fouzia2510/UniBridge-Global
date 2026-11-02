<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatGroupMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChatGroupController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $groupsQuery = ChatGroup::query()
            ->with(['latestMessage.sender'])
            ->withCount('members')
            ->withMax('messages', 'created_at');

        if ($user->role !== User::ROLE_ADMIN) {
            $groupsQuery->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $groups = $groupsQuery
            ->orderByRaw(
                'COALESCE(messages_max_created_at, chat_groups.created_at) DESC'
            )
            ->paginate(12);

        return view('chat-groups.index', compact('groups'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()->role === User::ROLE_ADMIN, 403);

        $users = User::query()
            ->where('is_approved', true)
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('chat-groups.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->role === User::ROLE_ADMIN, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => [
                'nullable', 'string', 'max:255',
                'required_with:contacts.*.phone',
            ],
            'contacts.*.phone' => [
                'nullable', 'string', 'max:30',
                'required_with:contacts.*.name',
            ],
        ]);

        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')
                ->store('chat-group-avatars', 'public');
        }

        try {
            $group = DB::transaction(function () use (
                $validated,
                $avatarPath
            ) {
                $group = ChatGroup::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'avatar_path' => $avatarPath,
                    'created_by' => Auth::id(),
                ]);

                // The creator is automatically a group owner and member.
                ChatGroupMember::create([
                    'chat_group_id' => $group->id,
                    'user_id' => Auth::id(),
                    'member_type' => 'user',
                    'role' => 'owner',
                    'added_by' => Auth::id(),
                ]);

                $userIds = collect($validated['user_ids'] ?? [])
                    ->unique()
                    ->reject(fn ($id) => (int) $id === (int) Auth::id());

                $approvedUserIds = User::query()
                    ->whereIn('id', $userIds)
                    ->where('is_approved', true)
                    ->pluck('id');

                foreach ($approvedUserIds as $userId) {
                    ChatGroupMember::create([
                        'chat_group_id' => $group->id,
                        'user_id' => $userId,
                        'member_type' => 'user',
                        'role' => 'member',
                        'added_by' => Auth::id(),
                    ]);
                }

                foreach (($validated['contacts'] ?? []) as $contact) {
                    $name = trim($contact['name'] ?? '');
                    $phone = trim($contact['phone'] ?? '');

                    if ($name === '' && $phone === '') {
                        continue;
                    }

                    if ($name === '' || $phone === '') {
                        throw new \InvalidArgumentException(
                            'Each phone contact needs both a name and a phone number.'
                        );
                    }

                    ChatGroupMember::create([
                        'chat_group_id' => $group->id,
                        'user_id' => null,
                        'member_type' => 'contact',
                        'contact_name' => $name,
                        'phone_number' => $phone,
                        'role' => 'member',
                        'added_by' => Auth::id(),
                    ]);
                }

                return $group;
            });
        } catch (\InvalidArgumentException $exception) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }

            return back()
                ->withInput()
                ->withErrors(['contacts' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('chat-groups.show', $group)
            ->with('success', 'Group created successfully.');
    }

    public function show(ChatGroup $chatGroup): View
    {
        $user = Auth::user();

        $isMember = $chatGroup->members()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless(
            $isMember || $user->role === User::ROLE_ADMIN,
            403
        );

        $chatGroup->load([
            'members.user',
            'creator',
        ]);

        $messages = $chatGroup->messages()
            ->with('sender')
            ->latest()
            ->paginate(50);

        $messages->setCollection(
            $messages->getCollection()->reverse()->values()
        );

        return view('chat-groups.show', compact('chatGroup', 'messages'));
    }

    public function storeMessage(
        Request $request,
        ChatGroup $chatGroup
    ): RedirectResponse {
        $user = Auth::user();

        $isMember = $chatGroup->members()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($isMember, 403);

        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:5000'],
        ]);

        ChatGroupMessage::create([
            'chat_group_id' => $chatGroup->id,
            'sender_user_id' => $user->id,
            'sender_contact_name' => null,
            'message_text' => trim($validated['message_text']),
            'message_type' => 'text',
        ]);

                return redirect()
            ->route('chat-groups.show', $chatGroup)
            ->withFragment('latest-message');
    }

    public function fetchNewMessages(
        Request $request,
        ChatGroup $chatGroup
    ) {
        $user = Auth::user();

        $isMember = $chatGroup->members()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless(
            $isMember || $user->role === User::ROLE_ADMIN,
            403
        );

        $validated = $request->validate([
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $afterId = (int) ($validated['after_id'] ?? 0);

        $messages = $chatGroup->messages()
            ->with('sender:id,name')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->sender?->name
                        ?? $message->sender_contact_name
                        ?? 'Unknown',
                    'sender_user_id' => $message->sender_user_id,
                    'message_text' => $message->message_text,
                    'message_type' => $message->message_type,
                    'created_at' => $message->created_at
                        ? $message->created_at->toIso8601String()
                        : null,
                ];
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }
}