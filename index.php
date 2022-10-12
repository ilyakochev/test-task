<?php 

require dirname( __FILE__ ) . '\functions.php';

?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
	<link rel="stylesheet" href="style.css">
	<title>Тестовое задание</title>
</head>
<body>
	<header>
		<div class="container-fluid header">
			<div class="row  justify-content-center">
				<div class="col-md-12"><h1>Тестовое задание</h1></div>
			</div>
		</div>
	</header>
	<section id="form-section">
		<div class="container">
			<div class="row">
				<div class="col-md-12 block">
					<div><h2>Форма</h2></div>
					<div class="form">
						<form action="#" id="add_message_form" method="POST">
							<label for="user_name" >Имя:</label><br>
							<input type="text" name="user_name" id="user_name" required><br>
							<label for="email">E-Mail:</label><br>
							<input type="email" name="email" id="email" required><br>
							<label for="CAPTCHAl">CAPTCHA:</label><br>
							<input type="text" name="CAPTCHA" id="CAPTCHA" required><br>
							<label for="text_message">Ваше сообщение:</label><br>
							<textarea name="text_message" id="text_message" cols="40" rows="5" required></textarea><br>
							<input type="submit" name="message_submit" class="button" id="message_submit" value="Отправить">
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="messages-section">
		<div class="container">
			<div class="row">
				<div class="col-md-12 block">
					<div><h2>Сообщения</h2></div>
					<div class=message_table>
						<?php 
							$disp_messages = new Message;
							$disp_messages-> showMessages();
						?>
					</div>
				</div>
			</div>
		</div>	
	</section>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
	<script src="scripts.js"></script>
</body>
</html>

<?php 
		
	if ( isset( $_POST['message_submit'] ) ) {
		if ( !empty( $_POST['user_name'] ) & !empty( $_POST['email'] ) & !empty( $_POST['text_message'] ) ) {
			
			$user_name = $_POST['user_name'];
			$email = $_POST['email'];
			$text_message = $_POST['text_message'];
			$mesage_date = date("d-m-Y H:i:s");

			//-------- Проверка E-mail при помощи регулярных выражений (при необходимости) ---------
			// if (preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,15})$/i", $email)) {
			//    echo "Адрес указан корректно.";
			// }else{
			//    echo "E-mail адрес " .$email. " указан неверно.\n";
			// }

			if ( filter_var( $email, FILTER_VALIDATE_EMAIL )) {
				if (!empty($_SERVER['HTTP_CLIENT_IP']))   
				{
					$ip_address = $_SERVER['HTTP_CLIENT_IP'];
				}
				elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
					{
						$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
					}
				else
				  {
				    $ip_address = $_SERVER['REMOTE_ADDR'];
				  }

				$browser = $_SERVER['HTTP_USER_AGENT'];
				$messages = new Message;
				$text_message = tagCleaner( $text_message );
				$messages-> sendMessage($mesage_date, $browser, $ip_address, $user_name, $email, $text_message);
			} else {
				echo "E-mail адрес " .$email. " указан неверно.\n";
			}

		} else {
			echo 'Заполненны не все обязательные поля!';
		}
	}

?>	