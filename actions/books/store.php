<?php
session_start();
require_once __DIR__ . "/../../config/database.php"; #dir digunakan untuk absolute path, __DIR__ artinya secara pasti mengambil path dari file ini sendiri, jadi tidak perlu repot-repot menulis ../../config/database.php lalu harus sama persis nama pathnya supaya tidak error
require_once __DIR__ . "/../../validations/book-validation.php";

if (isset($_POST['store']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction(); #memulai transaksi dan pakai transaction supaya tidak perlu cape cape mengurusi error (terjadi miss data) dan tidak bersifat tidak wjaib tapi lebih baik pakai transaction supaya lebih aman, karena kalau tidak pakai transaction maka jika ada error di tengah-tengah proses maka data yang sudah masuk ke database tidak bisa dihapus, sehingga akan terjadi miss data, tapi kalau pakai transaction maka jika ada error di tengah-tengah proses maka semua data yang sudah masuk ke database akan dihapus (rollback) sehingga tidak terjadi miss data.

        $request = getBookRequest($_POST);
        $errors = validateBook($request);

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $errors;

            header('Location: ../../pages/books/create.php');
            exit;
        }

        #tujuan bikin variabel adalah untuk mengindari penulisan $_POST berulang-ulang, jadi cukup sekali saja ditulis di variabel, lalu dipanggil di querynya dan serta supaya tidak duplikat (nama yang dipakai harus konsisten, misal di form inputnya name="title" maka di variabelnya harus sama juga $title, begitu juga di querynya :title)

        #menggunakan htmlspecialchars() untuk menghindari XSS (Cross-Site Scripting) dengan cara mengubah karakter-karakter khusus menjadi entitas HTML, sehingga input yang dimasukkan oleh pengguna tidak akan dieksekusi sebagai kode HTML atau JavaScript.

        #misal < akan menjadi &lt;, lalu > akan menjadi &gt;, sehingga jika ada user yang mencoba memasukkan script jahat, maka script tersebut tidak akan dijalankan oleh browser, melainkan hanya ditampilkan sebagai teks biasa.

        $query = "insert into books (title, isbn, year, stock, description, category_id)
                 values (:title, :isbn, :year, :stock, :description, :category_id)
                 ";
        #:title digunakan supaya kita bisa mengisinya jadi variabel $title dan values tidak bisa double karna pakainya false bukan true

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $request['title'],
            'isbn' => $request['isbn'],
            'year' => $request['year'],
            'stock' => $request['stock'],
            'description' => $request['description'],
            'category_id' => $request['category_id'],
        ]);
        #kalau tidak ada inputan tidak perlu pakai array sehingga cukup pakai $stmt->execute() saja, tapi kalau ada inputan maka harus pakai array di dalam execute, dan di dalam arraynya harus sesuai dengan nama yang ada di bagian values, misal :title maka di dalam arraynya harus 'title' => $title, begitu juga seterusnya.

        $bookId = $pdo->lastInsertId(); #mengambil id dari data terbaru yang dibikin suapaya bisa dimasukkan ke tabel book_author, karena di tabel book_author ada foreign key book_id yang mengacu ke id di tabel books, jadi harus diambil id dari data terbaru yang dimasukkan ke tabel books.

        $query = "insert into book_author (book_id, author_id) values (:book_id, :author_id)";
        $stmt = $pdo->prepare($query);

        $authorIds = $request['author_ids'];
        foreach ($authorIds as $authorId) {
            $stmt->execute([
                'book_id' => $bookId,
                'author_id' => htmlspecialchars($authorId)
            ]);
        }

        $pdo->commit(); #kalau semua query berhasil maka commit, kalau ada error maka akan di rollback, sehingga tidak ada data yang masuk ke database.

        header('location: ../../pages/books/index.php');
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

?>