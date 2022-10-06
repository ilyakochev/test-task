<?php

require_once dirname( __FILE__ ) . '\config.php'; //-------- Подключение к конфигуроционному файлу БД
date_default_timezone_set('Europe/Moscow');

class DBconnect { //----Класс подключения к базе данных
	private $connect;

	function connect () {
		global $db_config;

		try {
			$dsn = 'mysql:dbname='. $db_config['name'] .';host='. $db_config['host'] .';charset='. $db_config['charset'];
			$this->connect = new PDO( $dsn, $db_config['user'], $db_config['password'] );
			$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo 'Ошибка отправки сообщения.';
			die();
		}
		return $this->connect;
	}

}

class Message extends DBconnect{ //-----Класс Сообщений
	private $db_connect;
	private $message_date;
	private $browser = 'browser';
	private $ip;
	private $user_name;
	private $email;
	private $text_message;
	private $order_type;
	private $order_trigger_link;

	function sendMessage( $message_date, $browser, $ip, $user_name, $email, $text_message ) { //-------- Метод записи сообщений в БД
		$db_connect = $this->connect();
		$send_message_sql = 'INSERT INTO messages( message_date, browser, ip, user_name, email, text_message) VALUES ( ?, ?, ?, ?, ?, ? )';
		$statement = $db_connect->prepare( $send_message_sql );
		$statement->execute( array( $message_date, $browser, $ip, $user_name, $email, $text_message ) );
	}

	function showMessages(){ //-------- Метод чтения сообщений из БД
		$db_connect = $this->connect();
		$show_messages_sql = 'SELECT * FROM messages';
		$statement = $db_connect->prepare( $show_messages_sql );
		$statement->execute();
		$results = $statement->fetch( PDO::FETCH_ASSOC );

// ------------------------------------------ВЫВОД ТАБЛИЦЫ СООБЩЕНИЙ-------------------------------------------------------------
		$per_page = 5;
		$cur_page = 1;
		if ( !isset( $_GET[ 'order_type' ] )){
			$order_type = 'message_date';
		} else {
			$order_type = $_GET[ 'order_type' ];
		}

		if( !isset ( $_GET['order_trigger'] )){
			$order_trigger = 'DESC';
		} else {
			$order_trigger = $_GET['order_trigger'];
		}

		if( $order_trigger == 'DESC' ){
			$order_trigger_link = 'ASC';
		} else if ( $order_trigger == 'ASC' ){
			$order_trigger_link = 'DESC';
		} else {
			$order_trigger_link = 'DESC';
		}

		if ( isset( $_GET[ 'messages_page' ] )){
			$page = $_GET[ 'messages_page' ];
		} else {
			$page = 0;
		}

		function orderLink( $order_type, $order_trigger_link ){ //-------- Метод формирующий ссылку сортировки таблицы
			$order_link = '?order_type=' . $order_type . '&order_trigger=' . $order_trigger_link;
			return $order_link;
		}

		if ( isset( $page ) && $page > 0 ){
			$cur_page = $page;
		}

		$start = ( $cur_page - 1 ) * $per_page;
		$sql_rows_count  = "SELECT FOUND_ROWS() FROM messages";
		$rows = $db_connect->query( $sql_rows_count )->fetchColumn();
		$num_pages = ceil($rows / $per_page);
		$page = 0;

		$sql_message = "SELECT * FROM messages ORDER BY $order_type $order_trigger LIMIT $start, $per_page";
		$messages_data = $db_connect->query( $sql_message )->fetchAll();
	?>
				<table>
				<tr>
					<th><a href="<?php echo orderLink( 'message_date', $order_trigger_link ); ?>">Дата <i class="fa-solid fa-sort"></i></a></th>
					<th><a href="<?php echo orderLink( 'user_name', $order_trigger_link ); ?>">Имя <i class="fa-solid fa-sort"></i></a></th>
					<th><a href="<?php echo orderLink( 'email', $order_trigger_link ); ?>">Электронная почта <i class="fa-solid fa-sort"></i></a></th>
					<th>Текст сообщения</th>
				</tr>
	<?php 
		$i = 1;	
		foreach ($messages_data as $message) {
			$i++;
			++$start;
			?>
				<tr>
					<td><?php echo $message[ 'message_date' ]; ?></td>
					<td><?php echo $message[ 'user_name' ]; ?></td>
					<td><?php echo $message[ 'email' ]; ?></td>
					<td><?php echo $message[ 'text_message' ]; ?></td>
				</tr>
			<?php 
		}
	?>
		</table>
	<?php 

		$prev_page = $cur_page-1;
		$next_page = $cur_page+1;

		if ( !isset( $_GET[ 'order_type' ] )){
			$link = '?messages_page=';
		} else {
			$link = '?order_type=' . $order_type . '&order_trigger=' . $order_trigger . '&messages_page=';
		}

		if ( ( $cur_page >= 2 ) & ( $cur_page < $num_pages ) ) { ?> 
			<a class="button" href="<?php echo $link . $prev_page; ?>"> <i class="fa-solid fa-caret-left"></i>  Предыдущая</a>
			<a class="button" href="<?php echo $link . $next_page; ?>"> Следующая <i class="fa-solid fa-caret-right"></i> </a> </span> 

			<?php
		} else {
			if ( $cur_page > 1 ) { ?> 
				<a class="button" href="<?php echo $link . $prev_page; ?>"> <i class="fa-solid fa-caret-left"></i> Предыдущая</a>
				<span class="disabled-button"> Следующая</span> 

				<?php
			} 
			if ( $cur_page < $num_pages ) { ?> 

				<span class="disabled-button"> Предыдущая</span>
				<a class="button" href="<?php echo $link . $next_page; ?>"> Следующая <i class="fa-solid fa-caret-right"></i></a> 

				<?php
			}
		}
	}
}

function tagCleaner( $dirty_text ){ //-------- Функция валидации вводимого текста средствами PHP
	$dirty_text = trim($dirty_text);
	$dirty_text= stripslashes($dirty_text);
	$dirty_text = strip_tags($dirty_text);
	$dirty_text = htmlspecialchars($dirty_text);
	
	return $dirty_text;
}

?>