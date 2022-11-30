 <!-- summary -->
 <?php
$statistics_type = getSetting( 'statistics-type', 'site_settings', 'circle');
if ( 'progress' === $statistics_type ) {
?>
 <!-- summary body -->
  
            <div id="stats-top" class="" style="display: block;">
                <div id="invoices_total">
                    <div class="row">
                    <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">
                              <?php
	                           $total_amount_orders = Modules\Orders\Entities\Order::sum('price');
	                           ?>
                                    <h3 class="text-muted _total">
                                        {{ digiCurrency($total_amount_orders) }}            
                                    </h3>
                                    <span class="text-info">
                                        @lang('others.statistics.total-orders')
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">

                             <?php

	                           $total_active_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Active')->sum('price');

	                           ?>

                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_active_orders_amount )}}           
                                    </h3>
                                    <span class="text-warning">
                                        @lang('others.statistics.active-orders')
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">
                               <?php

                             $total_pending_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Pending')->sum('price');

                             ?>
                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_pending_orders_amount )}}              
                                    </h3>
                                    <span class="text-danger">
                                        @lang('others.statistics.pending-orders')
                                    </span>
                                </div>
                            </div>
                        </div>

                    <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">
                             <?php

                             $total_cancelled_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Cancelledd')->sum('price');

                             ?>
                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_cancelled_orders_amount )}}              
                                    </h3>
                                    <span class="text-success">
                                        @lang('others.statistics.cancelled-orders')
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                 </div>

            <div class="panel_s mtop20">
                <div class="panel-body-dr">
                    <div class="row text-left quick-top-stats">
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-9">
                                    <a href="#" data-cview="invoices_1" onclick="dt_custom_view('invoices_1','.table-invoices','invoices_1',true); return false;">
                                        <h5 class="blue-text">
                                            @lang('others.statistics.total-orders')
                                        </h5>
                                    </a>
                                </div>
                                	
                                <?php
                           $total_orders = Modules\Orders\Entities\Order::count();
                           ?>
                                <div class="col-md-12 progress-12">
                                    <div class="col-md-7 text-right blue-text " style="font-size:25px;">
                                     {{$total_orders}}           
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                    <a href="#" data-cview="invoices_2" onclick="dt_custom_view('invoices_2','.table-invoices','invoices_2',true); return false;">
                                        <h5 class="blue-text">
                                            @lang('others.statistics.active')
                                        </h5>
                                    </a>
                                </div>


                         <?php
                           $total_active_orders = Modules\Orders\Entities\Order::where('status', '=', 'Active')->count();
                           
                           $percent = ($total_active_orders / $total_orders ) * 100;
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_active_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">

                                    <div class="progress-list no-margin">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                    <a href="#" data-cview="invoices_4" onclick="dt_custom_view('invoices_4','.table-invoices','invoices_4',true); return false;">
                                        <h5 class="blue-text">
                                            @lang('others.statistics.pending')
                                        </h5>
                                    </a>
                                </div>

                          <?php
                           $total_pending_orders = Modules\Orders\Entities\Order::where('status', '=', 'Pending')->count();
                           $percent = ($total_pending_orders / $total_orders ) * 100;
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_pending_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">
                                    <div class="progress-list no-margin">


                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                      <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                    <a href="#" data-cview="invoices_4" onclick="dt_custom_view('invoices_4','.table-invoices','invoices_4',true); return false;">
                                        <h5 class="blue-text">
                                            @lang('others.statistics.cancelled')
                                        </h5>
                                    </a>
                                </div>

                          <?php
                           $total_cancelled_orders = Modules\Orders\Entities\Order::where('status', '=', 'Cancelled')->count();
                           
                           $percent = ($total_cancelled_orders / $total_orders ) * 100;
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_cancelled_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">
                                    <div class="progress-list no-margin">


                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!--  end summary body -->
       


            <!-- end summary -->
@section('javascript') 
@parent
               <script>


                     (function() {
                        if(typeof(init_selectpicker) == 'function'){
                        init_selectpicker();
                    }
                    })();
                    </script>


             
       


@endsection

<?php
} else {
 ?>
 <!-- summary -->

            <div class="panel panel-default">

        <div class="panel-body table-responsive">
            
                <!-- summary body -->

                <div class="panel-default" aria-hidden="false">
   <div class="crm-invoice-summary">
       
           <div class="row">
               <div class="col-md-12">
                   <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.total-orders')
                       </div>
                       <div class="box-content">
                           <?php
                           $total_orders = Modules\Orders\Entities\Order::count();
                           ?>
                           <div class="sentTotal">
                               {{$total_orders}}
                           </div>
                       </div>
                       <div class="box-foot">
                           <div class="sendTime box-foot-left">
                               @lang('others.statistics.amount')
                               <br>
                               <?php
	                           $total_amount_orders = Modules\Orders\Entities\Order::sum('price');
	                           ?>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_amount_orders )}}
                                   </strong>
                               </span>
                           </div>
                       </div>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.active-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_active_orders = Modules\Orders\Entities\Order::where('status', '=', 'Active')->count();
                           
                           $percent = ($total_active_orders / $total_orders ) * 100;
                           ?>
                           <div class="easypiechart" id="easypiechart-teal" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                                <?php

	                           $total_active_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Active')->sum('price');

	                           ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_active_orders_amount )}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.active')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_active_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.pending-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_pending_orders = Modules\Orders\Entities\Order::where('status', '=', 'Pending')->count();
                           
                           $percent = ($total_pending_orders / $total_orders ) * 100;
                           ?>
                           <div class="easypiechart" id="easypiechart-blue" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                              <?php

                             $total_pending_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Pending')->sum('price');

                             ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_pending_orders_amount )}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.pending')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_pending_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                   </div>



                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.cancelled-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_cancelled_orders = Modules\Orders\Entities\Order::where('status', '=', 'Cancelled')->count();
                           
                           $percent = ($total_cancelled_orders / $total_orders ) * 100;
                           ?>
                           <div class="easypiechart" id="easypiechart-orange" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                                <?php

                             $total_cancelled_orders_amount = Modules\Orders\Entities\Order::where('status', '=', 'Cancelled')->sum('price');

                             ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_cancelled_orders_amount )}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.cancelled')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_cancelled_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                   </div>

             
             
       </div>
       </div>
    </div>

</div>
                <!--  end summary body -->


        </div>
    </div>


            <!-- end summary -->
@section('javascript') 
@parent
<script>
/**!
 * easyPieChart
 * Lightweight plugin to render simple, animated and retina optimized pie charts
 *
 
 **/

(function(root, factory) {
    if(typeof exports === 'object') {
        module.exports = factory(require('jquery'));
    }
    else if(typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else {
        factory(root.jQuery);
    }
}(this, function($) {

/**
 * Renderer to render the chart on a canvas object
 * @param {DOMElement} el      DOM element to host the canvas (root of the plugin)
 * @param {object}     options options object of the plugin
 */
var CanvasRenderer = function(el, options) {
	var cachedBackground;
	var canvas = document.createElement('canvas');

	el.appendChild(canvas);

	if (typeof(G_vmlCanvasManager) !== 'undefined') {
		G_vmlCanvasManager.initElement(canvas);
	}

	var ctx = canvas.getContext('2d');

	canvas.width = canvas.height = options.size;

	// canvas on retina devices
	var scaleBy = 1;
	if (window.devicePixelRatio > 1) {
		scaleBy = window.devicePixelRatio;
		canvas.style.width = canvas.style.height = [options.size, 'px'].join('');
		canvas.width = canvas.height = options.size * scaleBy;
		ctx.scale(scaleBy, scaleBy);
	}

	// move 0,0 coordinates to the center
	ctx.translate(options.size / 2, options.size / 2);

	// rotate canvas -90deg
	ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI);

	var radius = (options.size - options.lineWidth) / 2;
	if (options.scaleColor && options.scaleLength) {
		radius -= options.scaleLength + 2; // 2 is the distance between scale and bar
	}

	// IE polyfill for Date
	Date.now = Date.now || function() {
		return +(new Date());
	};

	/**
	 * Draw a circle around the center of the canvas
	 * @param {strong} color     Valid CSS color string
	 * @param {number} lineWidth Width of the line in px
	 * @param {number} percent   Percentage to draw (float between -1 and 1)
	 */
	var drawCircle = function(color, lineWidth, percent) {
		percent = Math.min(Math.max(-1, percent || 0), 1);
		var isNegative = percent <= 0 ? true : false;

		ctx.beginPath();
		ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, isNegative);

		ctx.strokeStyle = color;
		ctx.lineWidth = lineWidth;

		ctx.stroke();
	};

	/**
	 * Draw the scale of the chart
	 */
	var drawScale = function() {
		var offset;
		var length;

		ctx.lineWidth = 1;
		ctx.fillStyle = options.scaleColor;

		ctx.save();
		for (var i = 24; i > 0; --i) {
			if (i % 6 === 0) {
				length = options.scaleLength;
				offset = 0;
			} else {
				length = options.scaleLength * 0.6;
				offset = options.scaleLength - length;
			}
			ctx.fillRect(-options.size/2 + offset, 0, length, 1);
			ctx.rotate(Math.PI / 12);
		}
		ctx.restore();
	};

	/**
	 * Request animation frame wrapper with polyfill
	 * @return {function} Request animation frame method or timeout fallback
	 */
	var reqAnimationFrame = (function() {
		return  window.requestAnimationFrame ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame ||
				function(callback) {
					window.setTimeout(callback, 1000 / 60);
				};
	}());

	/**
	 * Draw the background of the plugin including the scale and the track
	 */
	var drawBackground = function() {
		if(options.scaleColor) drawScale();
		if(options.trackColor) drawCircle(options.trackColor, options.lineWidth, 1);
	};

  /**
    * Canvas accessor
   */
  this.getCanvas = function() {
    return canvas;
  };
  
  /**
    * Canvas 2D context 'ctx' accessor
   */
  this.getCtx = function() {
    return ctx;
  };

	/**
	 * Clear the complete canvas
	 */
	this.clear = function() {
		ctx.clearRect(options.size / -2, options.size / -2, options.size, options.size);
	};

	/**
	 * Draw the complete chart
	 * @param {number} percent Percent shown by the chart between -100 and 100
	 */
	this.draw = function(percent) {
		// do we need to render a background
		if (!!options.scaleColor || !!options.trackColor) {
			// getImageData and putImageData are supported
			if (ctx.getImageData && ctx.putImageData) {
				if (!cachedBackground) {
					drawBackground();
					cachedBackground = ctx.getImageData(0, 0, options.size * scaleBy, options.size * scaleBy);
				} else {
					ctx.putImageData(cachedBackground, 0, 0);
				}
			} else {
				this.clear();
				drawBackground();
			}
		} else {
			this.clear();
		}

		ctx.lineCap = options.lineCap;

		// if barcolor is a function execute it and pass the percent as a value
		var color;
		if (typeof(options.barColor) === 'function') {
			color = options.barColor(percent);
		} else {
			color = options.barColor;
		}

		// draw bar
		drawCircle(color, options.lineWidth, percent / 100);
	}.bind(this);

	/**
	 * Animate from some percent to some other percentage
	 * @param {number} from Starting percentage
	 * @param {number} to   Final percentage
	 */
	this.animate = function(from, to) {
		var startTime = Date.now();
		options.onStart(from, to);
		var animation = function() {
			var process = Math.min(Date.now() - startTime, options.animate.duration);
			var currentValue = options.easing(this, process, from, to - from, options.animate.duration);
			this.draw(currentValue);
			options.onStep(from, to, currentValue);
			if (process >= options.animate.duration) {
				options.onStop(from, to);
			} else {
				reqAnimationFrame(animation);
			}
		}.bind(this);

		reqAnimationFrame(animation);
	}.bind(this);
};

var EasyPieChart = function(el, opts) {
	var defaultOptions = {
		barColor: '#ef1e25',
		trackColor: '#f9f9f9',
		scaleColor: '#dfe0e0',
		scaleLength: 5,
		lineCap: 'round',
		lineWidth: 3,
		size: 110,
		rotate: 0,
		animate: {
			duration: 1000,
			enabled: true
		},
		easing: function (x, t, b, c, d) { 
			t = t / (d/2);
			if (t < 1) {
				return c / 2 * t * t + b;
			}
			return -c/2 * ((--t)*(t-2) - 1) + b;
		},
		onStart: function(from, to) {
			return;
		},
		onStep: function(from, to, currentValue) {
			return;
		},
		onStop: function(from, to) {
			return;
		}
	};

	// detect present renderer
	if (typeof(CanvasRenderer) !== 'undefined') {
		defaultOptions.renderer = CanvasRenderer;
	} else if (typeof(SVGRenderer) !== 'undefined') {
		defaultOptions.renderer = SVGRenderer;
	} else {
		throw new Error('Please load either the SVG- or the CanvasRenderer');
	}

	var options = {};
	var currentValue = 0;

	/**
	 * Initialize the plugin by creating the options object and initialize rendering
	 */
	var init = function() {
		this.el = el;
		this.options = options;

		// merge user options into default options
		for (var i in defaultOptions) {
			if (defaultOptions.hasOwnProperty(i)) {
				options[i] = opts && typeof(opts[i]) !== 'undefined' ? opts[i] : defaultOptions[i];
				if (typeof(options[i]) === 'function') {
					options[i] = options[i].bind(this);
				}
			}
		}

		// check for jQuery easing
		if (typeof(options.easing) === 'string' && typeof(jQuery) !== 'undefined' && jQuery.isFunction(jQuery.easing[options.easing])) {
			options.easing = jQuery.easing[options.easing];
		} else {
			options.easing = defaultOptions.easing;
		}

		// process earlier animate option to avoid bc breaks
		if (typeof(options.animate) === 'number') {
			options.animate = {
				duration: options.animate,
				enabled: true
			};
		}

		if (typeof(options.animate) === 'boolean' && !options.animate) {
			options.animate = {
				duration: 1000,
				enabled: options.animate
			};
		}

		// create renderer
		this.renderer = new options.renderer(el, options);

		// initial draw
		this.renderer.draw(currentValue);

		// initial update
		if (el.dataset && el.dataset.percent) {
			this.update(parseFloat(el.dataset.percent));
		} else if (el.getAttribute && el.getAttribute('data-percent')) {
			this.update(parseFloat(el.getAttribute('data-percent')));
		}
	}.bind(this);

	/**
	 * Update the value of the chart
	 * @param  {number} newValue Number between 0 and 100
	 * @return {object}          Instance of the plugin for method chaining
	 */
	this.update = function(newValue) {
		newValue = parseFloat(newValue);
		if (options.animate.enabled) {
			this.renderer.animate(currentValue, newValue);
		} else {
			this.renderer.draw(newValue);
		}
		currentValue = newValue;
		return this;
	}.bind(this);

	/**
	 * Disable animation
	 * @return {object} Instance of the plugin for method chaining
	 */
	this.disableAnimation = function() {
		options.animate.enabled = false;
		return this;
	};

	/**
	 * Enable animation
	 * @return {object} Instance of the plugin for method chaining
	 */
	this.enableAnimation = function() {
		options.animate.enabled = true;
		return this;
	};

	init();
};

$.fn.easyPieChart = function(options) {
	return this.each(function() {
		var instanceOptions;

		if (!$.data(this, 'easyPieChart')) {
			instanceOptions = $.extend({}, options, $(this).data());
			$.data(this, 'easyPieChart', new EasyPieChart(this, instanceOptions));
		}
	});
};


}));



</script>


<script type="text/javascript">
	
	$(function() {
   $('#easypiechart-teal').easyPieChart({
       scaleColor: false,
       barColor: '#1ebfae'
   });
});

$(function() {
   $('#easypiechart-orange').easyPieChart({
       scaleColor: false,
       barColor: '#ffb53e'
   });
});

$(function() {
   $('#easypiechart-red').easyPieChart({
       scaleColor: false,
       barColor: '#f9243f'
   });
});

$(function() {
  $('#easypiechart-blue').easyPieChart({
      scaleColor: false,
      barColor: '#30a5ff'
  });
});

</script>
@endsection


<?php } ?>