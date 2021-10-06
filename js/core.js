jQuery.fn.wxButton = function(status){
	var button = $(this);
	if(status == 'load'){
		button.attr('disabled', 'disabled');
		button.find('.button-input-inner__text').css('display', 'none');
		button.find('.button-input__spinner').css('display', 'block');
	}else{
		button.removeAttr('disabled');
		button.find('.button-input-inner__text').css('display', 'inline-block');
		button.find('.button-input__spinner').css('display', 'none');
	}

}

jQuery.fn.wxMessage = function(message = '', type = ''){
    var block = $(this);

    block.removeClass('market-success').removeClass('market-error').find('span').text('');
    if(type == 'success'){
        block.addClass('market-success').find('span').text(message);
    }
    if(type == 'error'){
        block.addClass('market-error').find('span').text(message);
    }
}
jQuery.fn.wxPreload = function(action){
    var block = $(this);

    if(window.location.href.indexOf('market_core') == -1){
        return false;
    }

    if(action == 'show'){
        block.addClass('wxPreload');
        block.addClass('pos-relative').prepend(`
        <div class="market-preload" style="opacity:0">
            <svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 144 26" style="enable-background:new 0 0 144 26;" xml:space="preserve">
            <style type="text/css">
                .st0{fill:#000;}
            </style>
            <polygon class="st0" points="4.3,6 10.4,18.4 9.6,20 2.6,6 "></polygon>
            <path class="st0" d="M24,6l-7,14L9.6,6h1.7L17,16.9c0.3-0.6,0.6-1.2,1-1.9c0.4-0.7,0.7-1.5,1.1-2.2c0.4-0.8,0.7-1.5,1.1-2.3
                c0.4-0.8,0.7-1.4,1-2.1c0.3-0.6,0.5-1.1,0.7-1.6c0.2-0.4,0.3-0.7,0.4-0.8H24z"></path>
            <path class="st0" d="M26,0v7.3l-1.4,0l0-5.9H1.4l0,12.5H0V0H26z M26,12.1V26H0v-7.4h1.4l0,6h23.2l0-12.5L26,12.1z"></path>
            <rect x="22.3" y="6" class="st0" width="3.7" height="1.4"></rect>
            <rect x="0" y="18.6" class="st0" width="9.6" height="1.4"></rect>
            <polygon class="st0" points="46.1,5.3 52.6,18.7 51.8,20.4 44.2,5.3 "></polygon>
            <path class="st0" d="M67.3,5.3l-7.6,15.1l-8-15.1h1.8l6.1,11.8c0.3-0.6,0.7-1.3,1-2.1c0.4-0.8,0.8-1.6,1.2-2.4
                c0.4-0.8,0.8-1.7,1.2-2.5c0.4-0.8,0.7-1.5,1.1-2.2c0.3-0.7,0.6-1.2,0.8-1.7c0.2-0.5,0.4-0.8,0.4-0.9H67.3z"></path>
            <polygon class="st0" points="81.8,5.3 81.8,6.8 73.5,6.8 73.5,12.1 80.4,12.1 80.4,13.6 73.5,13.6 73.5,18.9 81.8,18.9 81.8,20.4
                72,20.4 72,5.3 "></polygon>
            <polygon class="st0" points="86.1,20.4 86.1,5.3 87.6,5.3 87.6,18.9 95.8,18.9 95.8,20.4 "></polygon>
            <path class="st0" d="M106.1,6.8h-4.5v5.4h4.5c0.1,0,0.2,0,0.4-0.1c0.2-0.1,0.5-0.2,0.7-0.4c0.3-0.2,0.5-0.5,0.7-0.8
                c0.2-0.4,0.3-0.9,0.3-1.5c0-0.6-0.1-1.1-0.3-1.5c-0.2-0.4-0.5-0.6-0.7-0.8c-0.3-0.2-0.5-0.3-0.7-0.3C106.3,6.8,106.1,6.8,106.1,6.8z
                 M107.3,13.6h-5.7v5.3h5.7c0.1,0,0.2,0,0.4-0.1c0.2,0,0.5-0.2,0.8-0.3c0.3-0.2,0.5-0.4,0.7-0.8c0.2-0.4,0.3-0.8,0.3-1.5
                c0-0.6-0.1-1.1-0.3-1.5c-0.2-0.4-0.5-0.6-0.7-0.8c-0.3-0.2-0.5-0.3-0.8-0.3C107.5,13.6,107.3,13.6,107.3,13.6z M106.3,5.3
                c0.1,0,0.2,0,0.4,0.1c0.2,0,0.4,0.1,0.6,0.2c0.2,0.1,0.4,0.2,0.7,0.3c0.2,0.1,0.4,0.3,0.6,0.5c0.2,0.2,0.4,0.4,0.6,0.7
                c0.2,0.3,0.3,0.6,0.4,1c0.1,0.4,0.2,0.9,0.2,1.4c0,0.7-0.1,1.3-0.3,1.9c-0.2,0.5-0.5,0.9-0.8,1.2c0.3,0.1,0.6,0.3,0.9,0.4
                c0.3,0.2,0.5,0.4,0.8,0.7c0.2,0.3,0.4,0.7,0.6,1.1c0.1,0.4,0.2,0.9,0.2,1.5c0,0.5-0.1,1-0.2,1.3c-0.1,0.4-0.3,0.7-0.4,1
                c-0.2,0.3-0.4,0.5-0.6,0.7c-0.2,0.2-0.4,0.4-0.6,0.5c-0.4,0.3-0.8,0.4-1.2,0.5c-0.4,0.1-0.6,0.1-0.7,0.1h-7.2V5.3h6
                C106.1,5.3,106.2,5.3,106.3,5.3z"></path>
            <polygon class="st0" points="125.9,5.3 125.9,6.8 117.7,6.8 117.7,12.1 124.5,12.1 124.5,13.6 117.7,13.6 117.7,18.9 125.9,18.9
                125.9,20.4 116.2,20.4 116.2,5.3 "></polygon>
            <polygon class="st0" points="142,20.4 137,14 132.3,20.4 130.1,20.4 135.9,12.9 130.1,5.3 132.3,5.3 137,11.7 142,5.3 144,5.3
                138.2,12.9 144,20.4 "></polygon>
            <rect class="market-logoLine" x="35.6" class="st0" width="1.4" height="26"></rect>
            </svg>
        </div>`);
    }else{
        block.find('.market-preload').remove();
        block.removeClass('pos-relative');
        block.removeClass('wxPreload');
    }
}

var marketCore = function () {
    	var self = this;
        var system = self.system;
        var user = AMOCRM.constant('user');
        var amouser = user.login;
        var amohash = user.api_key;
        var amodomain = AMOCRM.constant('account').subdomain;
        var widgetName = '';

		this.callbacks = {
            render: function () {
				widgetName = self.params.widget_code;

                if (!document.querySelector("#fancybox-script")) {
                    $('body').prepend('[incScripts]');
                    $('body').append('<div id="marketModal" style="display: none; width: 100%; max-width: 660px;"></div>')
                }
                
                
                if(window.location.href.indexOf('settings/widgets/' + widgetName + '/')) {
                    data = {
                        login: amouser,
                        hash: amohash,
                        domain: amodomain
                    };
                    $('#work_area').wxPreload('show');
                    self.crm_post(
                        'https://core.market.ru/view/', {
                            data
                        },
                        function (answer) {
                            $('#work_area').wxPreload('hide');
                            $('#work-area-'+widgetName).html(answer);
                        }
                    );
                }
                [initJS]

				return true;
			},
			init: function(){
				return true;
			},
			bind_actions: function(){
                [bindJS]

                $('body').on('submit', '#market-save-widget-settings', function(e){
                    e.preventDefault();

                    var button = $(this).find('button');
                    button.wxButton('load');

                    data = $(this).serialize();

                    data = {
                      login: amouser,
                      hash: amohash,
                      domain: amodomain,
                      settings: data
                    };


                    self.crm_post(
                      'https://core.market.ru/widgets/' + code + '/?action=saveSettings', {
                          data
                      },
                      function (data) {
                          data = JSON.parse(data);
                          if(data.success == true){
                            $('#market-message').addClass('market-success');
                            $('#market-message span').text(data.message);
                          }
                          button.wxButton('active');
                      }
                    );
                });

                $('body').on('click', '.market-widget-disable', function(e){

                	var button = $(this);

                	data = {
                        login: amouser,
                        hash: amohash,
                        domain: amodomain
                    };
                    self.crm_post(
                        'https://core.market.ru/widgets/' + code + '/?action=disable', {
                            data
                        },
                        function (data) {
	                        data = JSON.parse(data)
                			button.parent().addClass('disabled');
                			button.closest('#market-widget-settings').find('.market-widget-content').slideUp(500);
                        }
                    );
                });

                $('body').on('click', '.market-widget-enable', function(e){

                	var button = $(this);

                	data = {
                        login: amouser,
                        hash: amohash,
                        domain: amodomain
                    };
                    self.crm_post(
                        'https://core.market.ru/widgets/' + code + '/?action=enable', {
                            data
                        },
                        function (data) {
                        	console.log(data);
	                        data = JSON.parse(data)
	                        if(data.success == true){
	                			button.parent().removeClass('disabled');
	                			button.closest('#market-widget-settings').find('.market-widget-content').slideDown(500);
	                		}
                        }
                    );
                });

                $('body').on('click', '#close-widget-settings', function(){
            		data = {
                        login: amouser,
                        hash: amohash,
                        domain: amodomain
                    };
                    
                    $('#work-area-'+widgetName).wxPreload('show');
                    self.crm_post(
                        'https://core.market.ru/view/', {
                            data
                        },
                        function (answer) {
                            $('#work-area-'+widgetName).wxPreload('hide');
                            $('#work-area-'+widgetName).html(answer);
                        }
                    );
                });

                $('body').on('click', '.market-widget-item', function(){
                	code = $(this).data('code');

	                data = {
	                    login: amouser,
	                    hash: amohash,
	                    domain: amodomain
	                };
                    $('#work-area-'+widgetName).wxPreload('show');
                	self.crm_post(
	                    'https://core.market.ru/widgets/' + code + '/?action=showSettings', {
	                        data
	                    },
	                    function (data) {
                            console.log(data)
                            $('#work-area-'+widgetName).wxPreload('hide');
	                        data = JSON.parse(data)
                            $('#work-area-'+widgetName).html(data.html);
	                    }
	                );
                });

                $('body').on('change', '.market-details > .control-checkbox input[type=radio]', function(e){
                    $(this).closest('form').find('.market-detail-content').stop().slideUp(300);
                    $(this).closest('.market-details').find('.market-detail-content').stop().slideDown(300);
                });
                $('body').on('change', '.market-details > .control-checkbox input[type=checkbox]', function(e){
                    //$(this).closest('form').find('.market-detail-content').stop().slideUp(300);
                    $(this).closest('.market-details').find('.market-detail-content').stop().slideToggle(300);
                });

                return true;
			},
			settings: function(){
				// console.log(123);
				return true;
			},
            onSave: function () {
                console.log("object");
				return true;
			},
			destroy: function(){
                return true;
			},
			loadPreloadedData: function () {
				$('#'+widgetName).prepend('<div id="market-core-tab"></div>');
                data = {
                    login: amouser,
                    hash: amohash,
                    domain: amodomain,
                    id:  AMOCRM.constant('card_id')
                };

                self.crm_post(
                    'https://core.market.ru/widgets/sales/?action=loadData', {
                        data
                    },
                    function (data) {
                    	console.log(data)
                        data = JSON.parse(data)
                        $('#market-core-tab').html(data.html);
                    }
                );

                return new Promise(_.bind(function (resolve, reject) {
					resolve()
				}), this);
			},

			loadElements: function () {
			    return new Promise(_.bind(function (resolve, reject) {
					resolve()
				}), this);
			},

			linkCard: function () {
				return new Promise(_.bind(function (resolve, reject) {
					resolve();
				}), this);
			},

			searchDataInCard: function () {
				return new Promise(_.bind(function (resolve, reject) {
					resolve();
				}), this);
			},
			contacts: {
					//select contacts in list and clicked on widget name
					selected: function(){
					}
				},
			leads: {
					//select leads in list and clicked on widget name
					selected: function(){
					}
				},
			tasks: {
					//select taks in list and clicked on widget name
					selected: function(){
					}
				}
		};
		return this;
    };
