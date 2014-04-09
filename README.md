Yii amCharts Widget
===================

This is a wrapper to use the [amCharts](http://www.amcharts.com/) library with the Yii framework (PHP).

Requirements
------------

* Yii 1.1.14 or above
* PHP 5.3 or above

License
-------

This extension is free software, available under the terms of [MIT License](https://github.com/vundicind/yii-amcharts/blob/master/LICENSE).

Installation
-------------

### Installing with Composer

If you use [Composer](https://getcomposer.org/) to manage your project dependencies, you can install *yii-pickadate*
using the following commands:

```shell
php composer.phar config repositories.yii-amcharts vcs http://github.com/vundicind/yii-amcharts
php composer.phar require vundicind/yii-amcharts dev-master
```

### Installing by hand

Extract the contents of the archive under `protected/extensions/`.

*Important:* Downlaod the *JavaScript CHARTS* archive from http://www.amcharts.com/download/ and copy the contents of the `amcharts` directory (from archive) to the `assests` directory of extension. 

Configuration
-------------

If you installed the extension via Composer then you have to add the following alias to the config file:

```
    'aliases' => array(
        ...
        'amcharts' => realpath(__DIR__ . '/../../vendor/vundicind/yii-amcharts/src'),
        ...
    ),
```

Otherwise:

```
    'aliases' => array(
        ...
        'amcharts' => realpath(__DIR__ . '/../extensiosn/yii-amcharts/src'),
        ...
    ),
```

Usage
-----

### Getting Started

You may insert the following code into a view file:

```php
<?php
$this->widget('amcharts.AmChartsWidget', array(
    'htmlOptions' => array(
        'style' => 'width: 640px; height: 400px;',
    ),
    'options' => '{
        "type": "serial",
        "dataProvider": [{
        		"country": "USA",
	        	"visits": 4252
	        }, {
	        	"country": "China",
	        	"visits": 1882
        	}, {
        		"country": "Japan",
		        "visits": 1809
	        }],
        "categoryField": "country",
        "graphs": [{
                "type": "column",
                "valueField": "visits",
            }]
    }',
));
?>
```

Or you can use classic PHP arrays to confgure the widget.

```
<?php
$this->widget('amcharts.AmChartsWidget', array(
    'htmlOptions' => array(
        'style' => 'width: 640px; height: 400px;',
    ),
    'options' => array(
        "type" => "serial",
        "dataProvider" => array(
            array(
        		"country" => "USA",
	        	"visits" => 4252
	        ), 
            array(
	        	"country" => "China",
	        	"visits" => 1882
        	),
            array(
        		"country" => "Japan",
		        "visits" => 1809
	        )
        ),
        "categoryField" => "country",
        "graphs" => array(
            array(
                "type" => "column",
                "valueField" => "visits",
            )
        )
    )
));
?>
```

This example was taken from the tutorial page [Your first chart with amCharts](http://www.amcharts.com/tutorials/your-first-chart-with-amcharts/).

### Another (more complex) example 

You may insert the following code into a view file:
```php
<?php

// generate some random data, quite different range
function generateChartData() {
    $chartData = array();
    $firstDate = new DateTime();
    $firstDate->modify("-5 days");

    for ($i = 0; $i < 1000; $i++) {
        // we create date objects here. In your data, you can have date strings
        // and then set format of your dates using chart.dataDateFormat property,
        // however when possible, use date objects, as this will speed up chart rendering.
        $newDate = new DateTime($firstDate->format("Y-m-d"));
        $newDate->modify(($i == 1) ? "+$i day" : "+$i days");

        $visits = round(rand(0, 1) * (40 + $i / 5)) + 20 + $i;

        $chartData[] = array(
            "date" => $newDate->format("Y-m-d"),
            "visits" => $visits
        );
    }
    return $chartData;
}

$chartData = generateChartData();
?>

<?php
$this->widget('amcharts.AmChartsWidget', array(
    'htmlOptions' => array(
        'style' => 'width: 900px; height: 400px',
    ),
    'cfg' => array(
        'globalVar' => true,
    ),
    'options' => '{
        "type": "serial",
        "theme": "none",
        "pathToImages": "http://www.amcharts.com/lib/3/images/",
        "dataProvider": ' . CJSON::encode($chartData) . ',
        "valueAxes": [{
            "axisAlpha": 0.2,
            "dashLength": 1,
            "position": "left"
        }],
        "graphs": [{
            "id":"g1",
            "balloonText": "[[category]]<br /><b><span style=\'font-size:14px;\'>value: [[value]]</span></b>",
            "bullet": "round",
            "bulletBorderAlpha": 1,
		    "bulletColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "title": "red line",
            "valueField": "visits",
		    "useLineColorForBulletBorder": true
        }],
        "chartScrollbar": {
            "autoGridCount": true,
            "graph": "g1",
            "scrollbarHeight": 40
        },
        "chartCursor": {
            "cursorPosition": "mouse"
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "axisColor": "#DADADA",
            "dashLength": 1,
            "minorGridEnabled": true
        },
	    "exportConfig":{
	      menuRight: "20px",
          menuBottom: "30px",
          menuItems: [{
          icon: "http://www.amcharts.com/lib/3/images/export.png",
          format: "png"	  
          }]  
	    }
    }',
    'events' => array(
        'rendered' => 'zoomChart',
    ),
));
?>

<script>
    var chart;
    // this method is called when chart is first inited as we listen for "dataUpdated" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        chart.zoomToIndexes(<?php echo count($chartData) - 40; ?>, <?php echo count($chartData) - 1; ?>);
    }
</script>
```

This example was taken from [Line Chart with Scroll and Zoom](http://www.amcharts.com/demos/line-chart-with-scroll-and-zoom/) example from the [Demos](http://www.amcharts.com/demos/) page.

Credits
-------

Inspired by:

* [eamchartwidget](http://www.yiiframework.com/extension/eamchartwidget)
* [yii2-amcharts](http://www.yiiframework.com/extension/yii2-amcharts)
* [highcharts](http://www.yiiframework.com/extension/highcharts)
* [highsoft](www.yiiframework.com/extension/highsoft)
