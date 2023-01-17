<?php 

// 独自エンドポイントの追加
function add_my_endpoints() {
  register_rest_route('custom/v0', '/instagram', [
    'callback' => 'fetch_instagram_data',
    'permission_callback' => '__return_true',
    'methods'  =>  WP_REST_Server::READABLE
  ]);
}
add_action('rest_api_init', 'add_my_endpoints');

// apiデータの取得
function fetch_instagram_data($params) {
  $target_file = 'instagram';
  if(isset($params['target'])){
    $target_file = $params['target'];
  }

  $data = file_get_contents(wp_upload_dir()['basedir'].'/data/'.$target_file.'.json');
  $data = mb_convert_encoding($data, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
  $data = json_decode($data, true);
  $res = $data;
  $response = new WP_REST_Response($res);
  $response->set_status(200);

  return $response;
}