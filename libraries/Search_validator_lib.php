<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 主にGETで取得した検索パラメーターをチェックし、
 * $this->db->where($search_where)で使用する$search_where変数と
 * ページネーションや検索結果ページで使用する$after_paramsを作成する
 * ライブラリになります。
 *
 * codeigniter内のForm_validationの処理を参考に作成し、
 * validation機能も、Form_validationを使用させていただいています。
 *
 * rulesの指定は、
 * field
 *
 * 便利系として簡易的に作成しているものですので、抜けや使いにくい点があるかと思いますがご了承ください。
 *
 */

/**
 * Codeigniter 3 Original Library
 *
 * @package	CodeIgniter
 * @license	http://opensource.org/licenses/MIT	MIT License
 *
 * @since   Version 1.0.0
 * @author  Selay
 */
class Search_validator_lib
{
	protected $CI;

	protected $search_where = array();
	protected $after_params = array();
	protected $page = 0;


	public function __construct($rules = array())
	{
		$this->CI =& get_instance();
	}


	/**
	 * Get where array
	 *
	 * @return     array
	 */
	public function get_search_where()
	{
		return $this->search_where;
	}


	/**
	 * get after params array
	 *
	 * @return     array
	 */
	public function get_after_params()
	{
		return $this->after_params;
	}


	/**
	 * get page number
	 *
	 * @return     number
	 */
	public function get_page()
	{
		return $this->page;
	}


	/**
	 * validation実行からのパラメーターをセット
	 *
	 * Run validator and set where, after_params array
	 * This function does all the work.
	 *
	 * @param array    input params
	 * @param array    valiation rules
	 * @param string   page key
	 */
	public function run($params, $rules = array(), $page_key = 'page')
	{
		$CI =& get_instance();
		$CI->load->library('form_validation');

		if (isset($params[$page_key]) && $CI->form_validation->is_natural_no_zero($params[$page_key])) {
			$this->page = $params[$page_key];
			unset($params[$page_key]);
		}

		foreach ($rules as $row) {
			$field  = $row['field'];
			$column = $row['column'];
			$rules  = $row['rules'];

			if ( ! isset($params[$field])) {
				continue;
			}

			$value = $params[$field];
			if ($value == '') { continue; }

			$valid_rules = explode('|', $rules);
			foreach ($valid_rules as $rule) {
				$param = false;

				// ルールの指定に引数がある場合
				if (preg_match('/(.*?)\[(.*)\]/', $rule, $match)) {
					$rule  = $match[1];
					$param = $match[2];
				}

				if (method_exists($CI->form_validation, $rule)) {
					if ($CI->form_validation->$rule($value, $param) == false) {
						// form_validation内にメソッドが存在し、結果がfalseの場合
						continue;
					}
				} elseif ( ! (function_exists($rule) && $rule($param) != true)) {
					// メソッドが存在し、結果がfalseの場合
					continue;
				} else {
					// メソッド自体が存在しない場合
					continue;
				}
			}

			$this->after_params[$field]  = html_escape($value);
			$this->search_where[$column] = $CI->security->xss_clean($value);

		}
	}
}