<?php
namespace cms;

use cms\View;
use think\Config;
use think\Request;
use lookfeel\common\Format;

class Controller extends \think\Controller
{

    /**
     * 标题
     *
     * @var unknown
     */
    protected $site_title = '';
    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::instance();
        }
        $this->getView();
        $this->request = $request;

        // 控制器初始化
        $this->_initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }
    }

    /**
     * 跳转链接
     *
     * @param number $code
     * @param string $msg
     * @param string $url
     * @param string $data
     * @param number $wait
     * @param array $header
     * @return mixed
     */
    protected function jump($code = 1, $msg = '', $url = null, $data = '', $wait = 3, $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Url::build($url);
        }
        $jump = Format::formatJump($code, $msg, $url, $data, $wait);
        if (Request::instance()->isAjax()) {
            responseReturn($jump, Config::get('default_ajax_return'), true, $header);
        } else {
            $this->site_title || $this->site_title = $msg;

            $this->assign('jump', $jump);

            return $this->fetch('common/jump');
        }
    }
    /**
     * 处理成功
     *
     * @param string $msg
     * @param string $url
     * @param string $data
     * @param number $wait
     * @param array $header
     * @return mixed
     */
    protected function success($msg = '', $url = '', $data = '', $wait = 3, array $header = [])
    {
        return $this->jump(1, $msg, $url, $data, $wait, $header);
    }
    /**
     * 发生错误
     *
     * @param string $msg
     * @param string $url
     * @param string $data
     * @param number $wait
     * @param array $header
     * @return mixed
     */
    protected function error($msg = '', $url = '', $data = '', $wait = 3, array $header = [])
    {
        return $this->jump(0, $msg, $url, $data, $wait, $header);
    }
    /**
     * 渲染模板
     *
     * @param string $template
     * @param array $vars
     * @param array $replace
     * @param array $config
     * @return string
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        // 页面标题
        $this->assign('site_title', $this->site_title);

        return $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * 视图对象
     *
     * @return \think\View
     */
    public function getView()
    {
        empty($this->view) && $this->view = new View();
        return $this->view;
    }

    /**
     * 魔术方法
     *
     * @param string $name
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }
}
