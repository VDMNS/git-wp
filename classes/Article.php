<?php

/**
004
 * Класс для обработки статей
005
 */

class Article

{

  // Свойства

 

  /**
012
  * @var int ID статей из базы данных
013
  */

  public $id = null;


  /**
017
  * @var int Дата первой публикации статьи
018
  */

  public $publicationDate = null;

 

  /**
022
  * @var string Полное название статьи
023
  */

  public $title = null;

  /**
027
  * @var string Краткое описание статьи
028
  */

  public $summary = null;

  /**
032
  * @var string HTML содержание статьи
033
  */

  public $content = null;

  /**
038
  * Устанавливаем свойства с помощью значений в заданном массиве
039
  *
040
  * @param assoc Значения свойств
041
  */

  public function __construct( $data=array() ) {

    if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];

    if ( isset( $data['publicationDate'] ) ) $this->publicationDate = (int) $data['publicationDate'];

    if ( isset( $data['title'] ) ) $this->title = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['title'] );

    if ( isset( $data['summary'] ) ) $this->summary = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['summary'] );

    if ( isset( $data['content'] ) ) $this->content = $data['content'];

  }

 

 

  /**
053
  * Устанавливаем свойств с помощью значений формы редактирования записи в заданном массиве
054
  *
055
  * @param assoc Значения записи формы
056
  */

 

  public function storeFormValues ( $params ) {

    // Сохраняем все параметры

    $this->__construct( $params );

    // Разбираем и сохраняем дату публикации

    if ( isset($params['publicationDate']) ) {

      $publicationDate = explode ( '-', $params['publicationDate'] );

 

      if ( count($publicationDate) == 3 ) {

        list ( $y, $m, $d ) = $publicationDate;

        $this->publicationDate = mktime ( 0, 0, 0, $m, $d, $y );

      }

    }

  }

 

 

  /**
076
  * Возвращаем объект статьи соответствующий заданному ID статьи
077
  *
078
  * @param int ID статьи
079
  * @return Article|false Объект статьи или false, если запись не найдена или возникли проблемы
080
  */

  public static function getById( $id ) {

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

    $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM articles WHERE id = :id";

    $st = $conn->prepare( $sql );

    $st->bindValue( ":id", $id, PDO::PARAM_INT );

    $st->execute();

    $row = $st->fetch();

    $conn = null;

    if ( $row ) return new Article( $row );

  }

 

 

  /**
095
  * Возвращает все (или диапазон) объектов статей в базе данных
096
  *
097
  * @param int Optional Количество строк (по умолчанию все)
098
  * @param string Optional Столбец по которому производится сортировка  статей (по умолчанию "publicationDate DESC")
099
  * @return Array|false Двух элементный массив: results => массив, список объектов статей; totalRows => общее количество статей
100
  */

  public static function getList( $numRows=1000000, $order="publicationDate DESC" ) {

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

    $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM articles

            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

 

    $st = $conn->prepare( $sql );

    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );

    $st->execute();

    $list = array();

 

    while ( $row = $st->fetch() ) {

      $article = new Article( $row );

      $list[] = $article;

    }

 

    // Получаем общее количество статей, которые соответствуют критерию

    $sql = "SELECT FOUND_ROWS() AS totalRows";

    $totalRows = $conn->query( $sql )->fetch();

    $conn = null;

    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );

  }

 


  /**
126
  * Вставляем текущий объект статьи в базу данных, устанавливаем его свойства.
127
  */


  public function insert() {


    // Есть у объекта статьи ID?

    if ( !is_null( $this->id ) ) trigger_error ( "Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR );

 

    // Вставляем статью

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

    $sql = "INSERT INTO articles ( publicationDate, title, summary, content ) VALUES ( FROM_UNIXTIME(:publicationDate), :title, :summary, :content )";

    $st = $conn->prepare ( $sql );

    $st->bindValue( ":publicationDate", $this->publicationDate, PDO::PARAM_INT );

    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );

    $st->bindValue( ":summary", $this->summary, PDO::PARAM_STR );

    $st->bindValue( ":content", $this->content, PDO::PARAM_STR );

    $st->execute();

    $this->id = $conn->lastInsertId();

    $conn = null;

  }


 

  /**
149
  * Обновляем текущий объект статьи в базе данных
150
  */

 

  public function update() {

 

    // Есть ли у объекта статьи ID?

    if ( is_null( $this->id ) ) trigger_error ( "Article::update(): Attempt to update an Article object that does not have its ID property set.", E_USER_ERROR );


    // Обновляем статью

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

    $sql = "UPDATE articles SET publicationDate=FROM_UNIXTIME(:publicationDate), title=:title, summary=:summary, content=:content WHERE id = :id";

    $st = $conn->prepare ( $sql );

    $st->bindValue( ":publicationDate", $this->publicationDate, PDO::PARAM_INT );

    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );

    $st->bindValue( ":summary", $this->summary, PDO::PARAM_STR );

    $st->bindValue( ":content", $this->content, PDO::PARAM_STR );

    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );

    $st->execute();

    $conn = null;

  }

  /**
172
  * Удаляем текущий объект статьи из базы данных
173
  */

  public function delete() {

    // Есть ли у объекта статьи ID?

    if ( is_null( $this->id ) ) trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );

    // Удаляем статью

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

    $st = $conn->prepare ( "DELETE FROM articles WHERE id = :id LIMIT 1" );

    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );

    $st->execute();

    $conn = null;

  }

 

}

 

?>
