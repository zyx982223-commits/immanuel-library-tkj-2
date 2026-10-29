<?php
require_once __DIR__ . "/../config/database.php";

function getBooks()
{
  global $pdo;
  $query = "select b.id, b.title, b.stock, c.name as category from books b
           left join categories c on b.category_id = c.id
          "; #pakai alias untuk mempersingkat penulisan nama tabel, misal b untuk books, c untuk categories, sehingga tidak perlu menulis books.title, categories.name, cukup b.title, c.name saja serta pakai b.id supaya mysql tidak bingung karena ada id di books dan categories, jadi harus spesifik menulis b.id supaya mysql tau yang mana yang dimaksud
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $books = $stmt->fetchAll();

  $query = "select ba.book_id, a.name from book_author ba
           left join authors a on ba.author_id = a.id
           ";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $bookAuthors = $stmt->fetchAll();

  $bookByAuthors = [];
  foreach ($bookAuthors as $bookAuthor) {
    $bookByAuthors[$bookAuthor['book_id']][] = $bookAuthor['name'];
  }
  foreach ($books as $index => $book) {
    $books[$index]['authors'] = $bookByAuthors[$book['id']] ?? [];
  }

  return $books;
}

function getBook($id)
{
  global $pdo;

  $query = "SELECT id, title, isbn, year, stock, category_id, description FROM books WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'id' => $id
    ]);

  $book = $stmt->fetch();
  #fetch mengambil satu data

  if(!$book){
    header("Location: ../pages/books/index.php");
    exit();
  }

  $query = "SELECT a.id, a.name FROM book_author ba
            LEFT JOIN authors a ON ba.author_id = a.id
            WHERE ba.book_id = :book_id";
  $stmt = $pdo -> prepare($query);
  $stmt->execute([
    'book_id' => $book['id']
  ]);

  $authorIds = $stmt->fetchAll();  #fecthAll mengambil semua data
  $book['author_ids'] = array_column($authorIds, 'id');
  
  return $book;
}

function isIsbnTaken($isbn, $IgnoredBookId) 
{
  global $pdo;

  $bookId = intval($IgnoredBookId);

  $query = "select count(id) from books where isbn = :isbn && id  != :book_id";

  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'isbn' => $isbn,
    'book_id' => $bookId
  ]);

  $isIsbnTaken = $stmt->fetchColumn() > 0;

  return $isIsbnTaken;
} 