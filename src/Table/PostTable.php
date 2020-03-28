<?php

namespace App\Table;

use App\Model\Post;
use App\PaginatedQuery;

final class PostTable extends Table{

    protected $table ="post";
    protected $class = Post::class;

    public function update (Post $post): void
    {
        $query = $this->pdo->prepare("UPDATE {$this->table} SET name = :name, slug = :slug, created_at = :created, content = :content WHERE id = :id");
        $ok = $query->execute([
            'id' => $post->getID(),
            'name' => $post->getName(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'created' => $post->getCreatedAt()->format('Y-m-d H:i:s')

        ]);
        if($ok === false){
            throw new \Exception("Impossible de modifier l'enregistrement dans la table {$this->table}");
        }
    }

    public function create (Post $post): void
    {
        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET name = :name, slug = :slug, created_at = :created, content = :content");
        $ok = $query->execute([
            'name' => $post->getName(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'created' => $post->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        if($ok === false){
            throw new \Exception("Impossible de créer l'enregistrement dans la table {$this->table}");
        }
        $post->setID($this->pdo->lastInsertId());
    }

    

    public function delete (int $id): void 
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $ok = $query->execute([$id]);
        if($ok === false){
            throw new \Exception("Impossible de supprimer l'enregistrement dans la table {$this->table}");
        }
    }



    public function findPaginated () {
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}", 
            $this->pdo
        );
        $posts = $paginatedQuery->getItems(Post::class);
        (new CategoryTable($this->pdo))->hydratePosts($posts);
        return [$posts, $paginatedQuery];
    }

    public function findPaginatedForCategory (int $categoryID)
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT p.* 
             FROM {$this->table} p
             JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = {$categoryID}
             ORDER BY created_at DESC",
            "SELECT COUNT(category_id) FROM post_category WHERE category_id = {$categoryID}"
     );
     $posts = $paginatedQuery->getItems(Post::class);
     (new CategoryTable($this->pdo))->hydratePosts($posts);
     return [$posts, $paginatedQuery];
    }
}
