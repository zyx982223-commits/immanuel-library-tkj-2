<?php
require_once __DIR__ . "/../../config/database.php";
if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $id = htmlspecialchars($_POST['id']);
        $title = htmlspecialchars($_POST['title']);
        $isbn = htmlspecialchars($_POST['isbn']);
        $year = htmlspecialchars($_POST['year']);
        $stock = htmlspecialchars($_POST['stock']);
        $categoryId = htmlspecialchars($_POST['category_id']);
        $description = htmlspecialchars($_POST['description']);

        $query = "update books set title = :title, isbn = :isbn, 
                  year = :year, stock = :stock, category_id = :category_id,
                  description = :description where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $title,
            'isbn' => $isbn,
            'year' => $year,
            'stock' => $stock,
            'category_id' => $categoryId,
            'description' => $description,
            'id' => $id            
        ]);

        $query = "delete from book_author where book_id = :book_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['book_id' => $id]);

        $query = 'insert into book_author (book_id, author_id) values (:book_id, :author_id)';
        $stmt = $pdo->prepare($query);
        $authorIds = isset($_POST['author_ids']) ? $_POST['author_ids'] : [];
        foreach ($authorIds as $authorId) {
            $stmt->execute([
                'book_id' => $id,
                'author_id' => htmlspecialchars($authorId)
            ]);
        }
        $pdo->commit();
        header("Location: ../../pages/books/index.php");
        
    } catch (PDOException $e) {
        $pdo->rollback();
        throw $e;
    }
    print_r($_POST);
}

?>