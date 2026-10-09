<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Columns added by this migration, in the order they are created.
     *
     * @var list<string>
     */
    private array $columns = ['phone', 'status', 'avatar'];

    /**
     * Run the migrations.
     *
     * Each column is guarded so the migration is safe to apply against a
     * table where one of the columns was already added by hand.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'suspended'])
                    ->default('active')
                    ->after('phone');
            }

            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existing = array_values(array_filter(
            $this->columns,
            static fn (string $column): bool => Schema::hasColumn('users', $column)
        ));

        if ($existing === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($existing): void {
            $table->dropColumn($existing);
        });
    }
};
