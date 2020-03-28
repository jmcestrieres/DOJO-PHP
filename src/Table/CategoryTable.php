<?php

namespace App\Table;

use PDO;
use App\Model\Category;

final class CategoryTable extends Table{

    protected $table = "category";
    protected $class = Category::class;

    /**
     * @param App\Model\Post[] $posts
     */
    public function hydratePosts (array $posts): void
    {
        $postByID = [];
        $ids = [];
        foreach($posts as $post) {
            $postByID[$post->getID()] = $post;
            $ids[] = $post->getID();
        }
        $categories = $this->pdo->
            query('SELECT c.*, pc.post_id
                FROM post_category pc
                JOIN category c ON c.id = pc.category_id
                WHERE pc.post_id IN (' . implode(',', array_keys($postByID)) . ')'
            )->fetchAll(PDO::FETCH_CLASS, $this->class);

        // On parcourt les catégories
        foreach($categories as $category) {
            $postByID[$category->getPostID()]->addCategory($category);
        }
    }
}