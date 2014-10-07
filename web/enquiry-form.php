<?php
header('Content-Type: text/html;charset=utf-8');
header('X-XSS-Protection: 0');

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    echo '<p>問い合わせ内容を送信しました。</p>';
    exit;
}
?>
<!DOCTYPE html>
<meta charset="utf-8" />
<link rel="stylesheet" href="/example.css">
<title>問い合わせフォーム</title>
<h1>問い合わせフォーム</h1>
<form action="./enquiry-form.php" method="post">
<dl>
<dt>お問い合わせ内容</dt>
<dd><textarea style="width: 300px; height: 300px;"><?php print isset($_GET['msg']) ? $_GET['msg'] : '' ?></textarea></dd>
</dl>
<p><input type="submit" value="送信" /></p>
</form>
