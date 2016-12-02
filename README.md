# フォーム検索などの簡易validator&配列整形ライブラリ

　主にGET検索時の入力チェック、Modelへの検索内容の作成、Viewへ渡す検索内容の作成を行う為に作成したものです。    
　別の場所で公開していましたが、今回こちらで公開することにしました。    
　便利系として簡易的に作成しているものですので、抜けや使いにくい点があるかと思いますがご了承ください。    
　テストケースが未作成で、まだチェックが甘いところがあるかもしれませんが、気軽にプルリクを入れてもらえると助かります。    


### 必要環境
* PHP 5.4以上
* Codeigniter 3.0.0以上

----

### メソッド解説

##### ■ run(array $param, array $rules, string $page_field = 'page') : void
　主要メソッドです。
　$paramsに検索元の連想配列を、$rulesに下記に記載している形式のルールの連想配列を指定してください。    
　第3引数でpage_fieldを別途指定する形になっています。    
　これは実行後のチェックOKのパラメーター配列(after_params)や、where用の配列にpageが入らないようにする為になっています。    

##### ■ get_search_where() : array
　run実行後、validatorチェックを通過した検証ルールの行のパラメーターで、columnで指定した文字列がキーの連想配列を取り出します。    
　runメソッド呼出前に取り出した場合は空配列が戻ります。    

##### ■ get_after_params() : array
　run実行後、validatorチェックを通過した検証ルールの行のパラメーターで、fieldで指定した文字列がキーの連想配列を取り出します。     
　runメソッド呼出前に取り出した場合は空配列が戻ります。    

##### ■ get_page() : int
　run実行後、$page_fieldの値で0以上の整数の場合、その数値を取り出します。    
　runメソッド呼出前に取り出した場合は0が戻ります。

------

### 配列を使用したルールの指定
　ルールの指定は下記の形式になります。
　Form_valiationとは違い、配列での指定のみ対応しています。

    $rules = [
        'field'  => 'フィールド名',             // $params内での連想配列のキー名
        'column' => 'テーブルで使用するカラム名',   // $this->db->where()で使用する為の連想配列のキー名
        'rules'  => 'ルール。複数ある場合は|区切り' // 指定の仕方はForm_validationと同じです。
    ];

------

### 使用例
　get検索で、id=[検索するID]の場合。(IDのルールは0以上の整数とします)

    $params = $this->input->get(null, true);
    $rules  = [
        ['field' => 'id', 'column' => 'id', 'rules' => 'is_natural_no_zero'],
    ];
    $this->load->library('search_validator_lib');
    $this->search_validator_lib->run($params, $rules);

    // $this->db->whereで使用する連想配列取得
    $search_where = $this->search_validator_lib->get_search_where();

    // viewに渡す検索パラメーターの取得
    $after_params = $this->search_validator_lib->get_after_params();
