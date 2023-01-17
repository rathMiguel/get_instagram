<?php 
namespace instagram;

class Instagram
{
  protected string $basic_url = 'https://graph.facebook.com/';
  protected string $create_json_filiname;
  protected array $upload_path = array();

  /**
   * instagram apiのURLパラメータ
   * @todo デフォルトの値をセットするようにする
   */
  protected array $url_params = array(
    'user_id' => '',
    'access_token' => '',
    'target_user' => '',
    'limit' => 10,
    'params' => ['id', 'media_type', 'media_url', 'owner', 'timestamp', 'caption', 'permalink'],
    'children'=> ['id', 'media_url', 'media_type']
  );

  public function __construct(string $filename, array $params){
    $this->create_json_filiname = $filename;
    $this->url_params = $params;
    return $this->upload_path = wp_upload_dir();
  }

  /**
   * Instagram APIのパスを結合
   */

  protected function setInstagramApiUrl(){
    $url_params = $url_children_params = '';

    if(!empty($this->url_params['params'])) $url_params = implode(',', $this->url_params['params']);
    if(!empty($this->url_params['children'])) {
      $url_children_params = implode(',', $this->url_params['children']);
      $url_children_params = ",children{{$url_children_params}}";
    }

    return $this->basic_url.$this->url_params['user_id'].'?fields=business_discovery.username('.$this->url_params['target_user'].'){media.limit('.$this->url_params['limit'].'){'.$url_params.$url_children_params.'}}&access_token='.$this->url_params['access_token'];
  }

  /**
   * APIデータが書き込まれたファイルの保存先
   */

  protected function setUploadFilePath(){
    return $this->upload_path['basedir'].'/data/'.$this->create_json_filiname.'.json';
  }

  /**
   * apiレスポンスの取得
   * 
   * @param String $json_url 取得するjsonのurl
   * @return String|Boolean 200が返ってきたらレスポンス結果を,それ以外はfalseを返す
   */

    public function setApiData(string $json_url){
    $context = stream_context_create(
      [
        "http" => [
          "ignore_errors" => true
        ]
      ]
    );
    $url = file_get_contents($json_url, false, $context);
    return $url;
  }

  /**
   * jsonファイルを作成
   * 作成先はwordpressのuploads/data/${create_json_filiname}.json
   */

    public function createJsonFile(){      
      if(!file_exists($this->upload_path['basedir'].'/data')){
        mkdir($this->upload_path['basedir'].'/data');
      }
    
      if($this->setApiData($this->setInstagramApiUrl())){
        return file_put_contents($this->upload_path['basedir'].'/data/'.$this->create_json_filiname.'.json', $this->setApiData($this->setInstagramApiUrl()));
      } else {
        return false;
      }
    }

  /**
   * jsonデータのデコード。
   * 
   * @param String $data 取得するjsonのurl
   * @return Array 連想配列に変換されたjsonデータ
   * 
   */
  protected function decodeJson($data){
    $data = mb_convert_encoding($data, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $data = json_decode($data, true);
    return $data;
  }

  public function getJsonData(){
    if(!file_exists($this->upload_path['basedir'].'/data/'.$this->create_json_filiname.'.json')) $this->createJsonFile();
  }
}
