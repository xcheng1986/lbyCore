<?php

namespace Core\Lib;

class Page {

	// 分页显示定制
	private $config = array(
	);

	/**
	 * 架构函数
	 * @param array $totalRows  总的记录数
	 * @param array $listRows  每页显示记录数
	 * @param array $parameter  分页跳转的参数
	 */
	public function __construct($totalRows, $listRows = 20, $parameter = array()) {

	}

	/**
	 * 组装分页链接
	 * @return string
	 */
	public function show() {

	}

}
