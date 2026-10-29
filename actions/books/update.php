<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/book-validation.php";

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $request = getBookRequest($_POST);
        $errors = validateBook($request, $request['id']);
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $request;

            header('Location: ../../pages/books/edit.php?id=' .urlencode($request['id']));
            exit;
        }


    $query = "UPDATE books SET title = :title, isbn = :isbn,
            year = :year, stock = :stock, category_id = :category_id,
            description = :description WHERE id = :id";

    $stmt = $pdo->prepare($query);            
    $stmt->execute([
        'title' => $request['title'],
        'isbn' => $request['isbn'],
        'year' => $request['year'],
        'stock' => $request['stock'],
        'category_id' => $request['category_id'],
        'description' => $request['description'],
        'id' => $request['id']
    ]);

    $query = "DELETE FROM book_author WHERE book_id = :book_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'book_id' => $request['id']
    ]);

    $query = "INSERT INTO book_author (book_id, author_id) VALUES (:book_id, :author_id)";
    $stmt = $pdo->prepare($query);
    $authorIds = isset($request['author_ids']) ? $request['author_ids'] : [];

    foreach ($authorIds as $authorId) {
        $stmt->execute([
            'book_id' => $request['id'],
            'author_id' => htmlspecialchars($authorId)
        ]);
    }

    $pdo->commit();
    header('Location: ../../pages/books/index.php');

    print_r($_POST);
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

#proses update buku dilakukan dengan menghapus semua data author yang terkait dengan buku tersebut di tabel book_author, kemudian menambahkan kembali data author yang baru sesuai dengan inputan dari form. Hal ini dilakukan untuk memastikan bahwa data author yang terkait dengan buku tersebut selalu sesuai dengan inputan terbaru dari form, sehingga tidak terjadi duplikasi atau ketidaksesuaian data.

#array column digunakan untuk mengambil data secara spesifik dan in array mengecek apakaha semua item ada di dalam array
?>