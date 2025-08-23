<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // 対象の取引（購入）
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();

            // レビューをした人／された人
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            // 評価スコア（1〜5想定）
            $table->unsignedTinyInteger('rating');

            $table->timestamps();

            // 同じ取引で同じ人が複数回レビューしないように
            $table->unique(['purchase_id', 'reviewer_id']);
            // 集計・参照のため
            $table->index(['reviewee_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
