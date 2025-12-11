<?php
// database/migrations/xxxx_xx_xx_create_user_devices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_device_logs')) {
            Schema::create('user_device_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subscriber_id');      // subscriber id
                $table->string('guard')->default('subscriber');
                $table->string('session_id')->nullable()->index();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_name')->nullable();
                $table->string('browser')->nullable();
                $table->string('platform')->nullable();
                $table->boolean('is_current')->default(false)->index();
                $table->timestamp('logged_in_at')->nullable();
                $table->timestamp('logged_out_at')->nullable();
                $table->timestamps();

                $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
