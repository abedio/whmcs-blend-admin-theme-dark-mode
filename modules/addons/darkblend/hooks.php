<?php

/***************************************************************************
// *                                                                       *
// * Blend Dark Mode                                                       *
// * This addon adds dark mode to the Blend admin theme                    *
// * Compatible with WHMCS Version: 8.x                                    *
// * https://github.com/WevrLabs-Group/WHMCS-Blend-Admin-Theme-Dark-Mode   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Maintained by: WevrLabs Hosting                                       *
// * Email: hello@wevrlabs.net                                             *
// * Website: https://wevrlabs.net                                         *
// *                                                                       *
// *************************************************************************/

use WHMCS\Database\Capsule;

# Stats call
function admin_blend_stats_hook($vars)
{
	$v8 = Capsule::select(Capsule::raw('SELECT value FROM tblconfiguration WHERE setting = "Version" LIMIT 1'))[0]->value;
	if (explode('.', $v8)[0] != '8') : return false;
	endif;
	
	$showTime		= Capsule::table('tbladdonmodules')->where('module', 'darkblend')->where('setting', 'datetime_enable')->first();
	$showTickets	= Capsule::table('tbladdonmodules')->where('module', 'darkblend')->where('setting', 'ticketcount_enable')->first();
	$ticketsTotal 	= Capsule::select(Capsule::raw('SELECT COUNT(t1.id) AS total FROM tbltickets AS t1 LEFT JOIN tblticketstatuses AS t2 ON t1.status = t2.title WHERE t2.showawaiting = "1" AND merged_ticket_id = "0"'))[0]->total;
	$awaitingTicketsJS = '';
	$time = '';

	if ($showTickets->value && $ticketsTotal > 0) {

		$ticketsAwaitCol 	= 'style="color: #f71616;font-size: 20px"';
		$tada 			 	= 'animation: tada 1s both infinite';
		$ticketText 		= $ticketsTotal . ' ' . AdminLang::trans('stats.ticketsawaitingreply');

		$awaitingTicketsJS 	= <<<HTML
        <li><a href="supporttickets.php" class="tickets-nav" data-toggle="tooltip" data-placement="bottom" title="{$ticketText}" data-original-title="{$ticketText}" style="word-wrap:break-word;{$tada}"><small class="v8navstatsul"><span class="icon-container"><i class="fad fa-comments"></i></span><span class="v8navstats" {$ticketsAwaitCol}>{$ticketsTotal}</span></small></a></li>
HTML;
	}

	if ($showTime->value) {
		$time = '<li class="nav-time" title="' . date('M d Y, H:i') . '"><small><span class="v8navstats"><span class="icon-container"><i class="icon fas fa-clock"></i></span><span class="nav-date">' . date('M d, H:i') . '</span><span class="nav-clock"></span></span></small></li>';
	}

	return <<<HTML
<script type="text/javascript">

	$(document).on('ready', function() {
		$('.navigation ul.right-nav').first('li').prepend('{$awaitingTicketsJS}{$time}');

		$("*[id=\'v8navstats\']").on("click", function(e) {
			e.preventDefault();
			$(e.currentTarget).parent("li").toggleClass("expanded");
		});

		$('#v8navstats').next('ul').css({"width": "340px", "left": "-134px"});

		$('span.v8navstats').css({"font-weight": "700"});
	});

</script>
HTML;
}
function admin_blend_change_theme($vars)
{
	if ($vars != 'blend') {
		return;
	}

	$hasCustomCss = file_exists(ROOTDIR . "/modules/addons/darkblend/custom.css");
	$buttonToggleClass = 'bootstrap-switch-off';
	$buttonToggleMargin = '-32px';
	$toggleButton = <<<HTML
<li style="margin: 7px 7px 0;"><div class="bootstrap-switch-mini bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-animate {$buttonToggleClass}" data-has-custom-css="{$hasCustomCss}" style="width: 66px;"><div class="bootstrap-switch-container" style="width: 96px; margin-left: {$buttonToggleMargin};"><span class="bootstrap-switch-handle-on bootstrap-switch-info" style="width: 32px;"><svg style="vertical-align: middle;padding-bottom: 3px;" width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.59954 12.4837C2.2537 13.6169 3.16552 14.5801 4.26117 15.2954C5.35681 16.0107 6.6055 16.4579 7.90613 16.6009C9.20676 16.7439 10.5228 16.5785 11.7476 16.1183C12.9725 15.6581 14.0718 14.916 14.9565 13.9519C13.2253 14.3374 11.4162 14.1605 9.79252 13.447C8.16879 12.7334 6.81507 11.5205 5.92827 9.98448C5.04147 8.4485 4.66786 6.66965 4.86177 4.90669C5.05569 3.14372 5.80702 1.48861 7.00648 0.182107C5.72924 0.466255 4.53686 1.04717 3.52588 1.87783C2.5149 2.70849 1.71372 3.76554 1.18722 4.9634C0.660724 6.16126 0.423695 7.46627 0.495326 8.77278C0.566958 10.0793 0.945237 11.3506 1.59954 12.4837Z" fill="white"/></svg></span><span class="bootstrap-switch-label" style="width: 32px;">&nbsp;</span><span class="bootstrap-switch-handle-off bootstrap-switch-warning" style="width: 32px;"><svg style="vertical-align: middle;padding-bottom: 3px;" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.62273 11.5118L2.52 12.4091L3.66545 11.27L2.76182 10.3664M7 3.18182C4.89364 3.18182 3.18182 4.89364 3.18182 7C3.18182 9.10636 4.89364 10.8182 7 10.8182C9.10636 10.8182 10.8182 9.10636 10.8182 7C10.8182 4.88727 9.10636 3.18182 7 3.18182ZM12.0909 7.63636H14V6.36364H12.0909M10.3345 11.27L11.48 12.4091L12.3773 11.5118L11.2382 10.3664M12.3773 2.54545L11.48 1.65455L10.3345 2.79364L11.2382 3.69727M7.63636 0H6.36364V1.90909H7.63636M3.66545 2.79364L2.52 1.65455L1.62273 2.54545L2.76182 3.69727L3.66545 2.79364ZM0 7.63636H1.90909V6.36364H0M7.63636 12.0909H6.36364V14H7.63636" fill="white"/></svg></span><input type="checkbox" class="twofa-toggle-switch"></div></div></li>
HTML;

	return <<<HTML
<script type="text/javascript">

	$(document).on('ready', function() {
		
		const \$li = $('{$toggleButton}').prependTo($('.navigation ul.right-nav'));
		const \$el = $('> .bootstrap-switch', \$li);
		const \$toggleContainer = $('.bootstrap-switch-container', \$el);

		if ('on' == localStorage.getItem("darkmode")) {
			\$el.toggleClass('bootstrap-switch-off bootstrap-switch-on');
			\$toggleContainer.css('margin-left', '0');
		}

		\$el.on('click', function(e) {
			e.preventDefault();
			$(this).prop('disabled', true);
			if ($(this).hasClass('bootstrap-switch-off')) {
				\$toggleContainer.css('margin-left', '0');
				$('head').append('<link href="../modules/addons/darkblend/css/dark-blend.css" rel="stylesheet" type="text/css" />');
				if ($(this).data('has-custom-css')) {
					$('head').append('<link href="../modules/addons/darkblend/css/dark-blend.css" rel="stylesheet" type="text/css" />');
				}

				localStorage.setItem("darkmode", "on");
			} else {
				\$toggleContainer.css('margin-left', '-32px');
				$('head link[href$="darkblend/css/dark-blend.css"]').remove();
				$('head link[href$="darkblend/custom.css"]').remove();
				localStorage.setItem("darkmode", "off");
			}

			$(this).toggleClass('bootstrap-switch-off bootstrap-switch-on');
			$(this).prop('disabled', false);
		});
	});

</script>
HTML;
}

function admin_head($vars)
{
	if ($vars['template'] != "blend") {
		return;
	}

	$files = '<link href="../modules/addons/darkblend/css/dark-blend.css" rel="stylesheet" type="text/css" />';
	if (file_exists(ROOTDIR . "/modules/addons/darkblend/custom.css")) {
		$files .= '<link href="../modules/addons/darkblend/custom.css?ver=' . time() . '" rel="stylesheet" type="text/css" />';
	}

	return <<<HTML
<script type="text/javascript">
	if ('on' == localStorage.getItem("darkmode")) {
		$('head').append('{$files}');
	}
</script>
HTML;
}

add_hook("AdminAreaHeadOutput", 1, "admin_head");
add_hook('AdminAreaHeaderOutput', 1, "admin_blend_stats_hook");
add_hook('AdminAreaHeaderOutput', 1, "admin_blend_change_theme");
