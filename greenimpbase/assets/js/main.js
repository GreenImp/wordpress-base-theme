/**
 * Copyright GreenImp Web - greenimp.co.uk
 *
 * Author: GreenImp Web
 * Date Created: 21/04/13 20:20
 */

;(function($, window, document){
	// add modal dialogue to image links
	$('.entry-content').find('a[href$="jpg"], a[href$="png"], a[href$="jpeg"]').bill('modal');
})(jQuery, window, document);