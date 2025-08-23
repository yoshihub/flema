<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageReadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // この取引のチャットを最後に読んだ時刻（未読数計算の基準）
            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            // 同じ取引で同じユーザー行は1つ
            $table->unique(['purchase_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_reads');
    }
}
