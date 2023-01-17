<?php 
  require_once(get_stylesheet_directory().'/inc/instagram.php');
  use instagram\Instagram as Instagram;
  $instagram = new Instagram(
    'instagram',
    INSTAPARAM
  );

  /**
   * instagram apiをjsonファイルに保存
   * （使用制限回避のためにログインユーザーがアクセスした時のみ更新）
   */
  if(is_user_logged_in()) $instagram->createJsonFile();
  
  /**
   * Instagramデータの取得
   * @todo 関数化するか検討
   */
  $instagram_data = file_get_contents(esc_url(home_url('/')).'wp-json/custom/v0/instagram');
  $instagram_data = json_decode($instagram_data, true);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hi, Instagram</title>
</head>
<body>
  <div class="container">
    <ul class="gallery-instagram" id="instafeed">
      <?php $count = 0; ?>
      <?php foreach($instagram_data["business_discovery"]["media"]["data"] as $key => $data): ?>
        <?php if($data["media_type"] !== "VIDEO" && $count < 10): ?>
          <li><a href="<?php echo $data["permalink"] ?>" target="_blank"><img src="<?php echo $data["media_url"] ?>" alt=""></a></li>
          <?php $count++ ?>
        <?php endif ?>
      <?php endforeach ?>
    </ul>
  </div>
</body>
</html>