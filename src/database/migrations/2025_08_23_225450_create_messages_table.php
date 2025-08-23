<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // 取引（購入）に紐づく
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            // 送信者（購入者 or 出品者）
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // 本文
            $table->string('content');
            // 画像（任意）: storageに保存したパスを想定
            $table->string('image_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
