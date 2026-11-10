<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SecurePaymentProofs extends Command
{
    protected $signature = 'payments:secure-proof-files';

    protected $description = 'Move referenced payment proofs from public to private storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;
        $alreadyPrivate = 0;

        Payment::query()
            ->whereNotNull('proof_path')
            ->orderBy('id')
            ->chunkById(100, function ($payments) use ($public, $private, &$moved, &$alreadyPrivate): void {
                foreach ($payments as $payment) {
                    $path = ltrim((string) $payment->proof_path, '/\\');

                    if ($path === '' || str_contains($path, '..')) {
                        $this->warn("Skipped invalid stored path for payment {$payment->id}.");
                        continue;
                    }

                    if (! $public->exists($path)) {
                        if ($private->exists($path)) {
                            $alreadyPrivate++;
                        }

                        continue;
                    }

                    if (! $private->exists($path)) {
                        $stream = $public->readStream($path);

                        if (! is_resource($stream)) {
                            throw new RuntimeException("Could not read payment proof {$payment->id}.");
                        }

                        try {
                            if (! $private->put($path, $stream)) {
                                throw new RuntimeException("Could not write private payment proof {$payment->id}.");
                            }
                        } finally {
                            fclose($stream);
                        }

                        if (! $private->exists($path)
                            || $private->size($path) !== $public->size($path)
                        ) {
                            throw new RuntimeException("Private payment proof verification failed for payment {$payment->id}.");
                        }

                        $moved++;
                    } else {
                        $alreadyPrivate++;
                    }

                    // Remove the public copy only after the private copy is verified.
                    if (! $public->delete($path)) {
                        throw new RuntimeException("Could not remove public payment proof {$payment->id}.");
                    }
                }
            });

        $this->info("Moved {$moved} payment proof(s) to private storage; {$alreadyPrivate} already had private copies.");

        return self::SUCCESS;
    }
}
