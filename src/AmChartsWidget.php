<?php

/**
 * AmChartsWidget class file.
 *
 * @author Radu Dumbrăveanu <vundicind@gmail.com>
 * @link https://github.com/vundicind/yii-amcharts/
 */
class AmChartsWidget extends CWidget
{

    protected $_baseScript = 'amcharts';

	/**
	 * @var array the tab configuration.
	 */
    public $options = array();


	/** @var string[] the Javascript event handlers. */
    public $events = array();
    public $setupOptions = array();

    /** @var array the HTML attributes for the widget container. */
    public $htmlOptions = array();
    public $scripts = array();
    public $embeddedScriptOptions = array();


    /**
     * Initializes the widget.
     */
    public function init()
    {
        // check if options parameter is a json string
        if (is_string($this->options)) {
            if (!$this->options = CJSON::decode($this->options)) {
                throw new CException('The options parameter is not valid JSON.');
            }
        }

        


        // merge options with default values
        $defaultOptions = array('type' => 'serial');
        $this->options = CMap::mergeArray($defaultOptions, $this->options);
        array_unshift($this->scripts, $this->_baseScript);

        $this->embeddedScriptOptions = CMap::mergeArray(array('chartVarGlobal' => false, 'chartVar' => 'chart'), $this->embeddedScriptOptions);

        switch ($this->options['type']) {
            case 'serial':
                $this->scripts[] = 'serial';
                break;
            case 'pie':
                $this->scripts[] = 'pie';
                break;
            case 'xy':
                $this->scripts[] = 'xy';
                break;
            case 'radar':
                $this->scripts[] = 'radar';
                break;
            case 'funnel':
                $this->scripts[] = 'funnel';
                break;
            case 'gauge':
                $this->scripts[] = 'gauge';
                break;
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        if (isset($this->htmlOptions['id'])) {
            $id = $this->htmlOptions['id'];
        } else {
            $id = $this->htmlOptions['id'] = $this->getId();
        }

        echo CHtml::openTag('div', $this->htmlOptions);
        echo CHtml::closeTag('div');

        // check if options['dataProvider'] is an instance of CActiveDataProvider or IDataProvider
        $newDataProvider = array();

        if ($this->options['dataProvider'] instanceof CActiveDataProvider) {
            $this->options['dataProvider'] = $this->options['dataProvider']->getData();

            foreach ($this->options['dataProvider'] as $modelData)
                $newDataProvider[] = $modelData->attributes;
        } else if ($this->options['dataProvider'] instanceof IDataProvider) {
            $newDataProvider = $this->options['dataProvider']->getData();
        }

        if (!empty($newDataProvider))
            $this->options['dataProvider'] = $newDataProvider;

        $jsOptions = CJavaScript::encode($this->options);
        $setupOptions = CJavaScript::encode($this->setupOptions);

        $embeddedScript = '';

        if (!empty($this->setupOptions)) {
            foreach ($this->setupOptions as $property => $value)
               $embeddedScript .= "AmCharts.$property = " . CJSON::encode($value) . ";";
        }

        $varStr = ($this->embeddedScriptOptions['chartVarGlobal']) ? '' : 'var ';
        $varName = $this->embeddedScriptOptions['chartVar'];
        $embeddedScript .= "{$varStr}{$varName} = AmCharts.makeChart(\"$id\", ($jsOptions));";

        if (!empty($this->events)) {
            foreach ($this->events as $event => $handler) {
                $js = new CJavaScriptExpression($handler);
                $embeddedScript .= "{$varName}.addListener(\"$event\", " . $js . ");";
            }
        }

        $this->registerScripts(__CLASS__ . '#' . $id, $embeddedScript);
    }


    /**
     * Publishes and registers the necessary script files.
     *
     * @param string the id of the script to be inserted into the page
     * @param string the embedded script to be inserted into the page
     */
    protected function registerScripts($id, $embeddedScript)
    {
        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1, YII_DEBUG);

        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');

        // register additional scripts
        $extension = YII_DEBUG ? '.js' : '.min.js';
        $cs->registerScriptFile("{$baseUrl}/{$this->_baseScript}{$extension}", CClientScript::POS_HEAD);
        foreach ($this->scripts as $script) {
            $cs->registerScriptFile("{$baseUrl}/{$script}{$extension}", CClientScript::POS_HEAD);
        }

        // register embedded script
        $cs->registerScript($id, $embeddedScript, CClientScript::POS_LOAD);
    }

} 
