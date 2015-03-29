(function($){
	$(document).ready(function(){
		var $tbl = $('.wp-list-table tbody').sortable({
			items: '> tr',
			cursor: 'move',
			handle: 'span.handle',
			axis: 'y',
			containment: 'table.widefat',
			cancel:	'.inline-edit-row',
			distance: 2,
			opacity: .8,
			tolerance: 'pointer',
			start: function(e, ui){
				ui.placeholder.height(ui.item.height());
			},
			helper: function(e, ui) {
				var children = ui.children();
				for ( var i=0; i<children.length; i++ ) {
					var selector = jQuery(children[i]);
					selector.width( selector.width() );
				};
				return ui;
			},
			stop: function(e, ui) {
				// remove fixed widths
				ui.item.children().css('width','');
			},
			update: function(e, ui) {
				var term_order = {},do_send,start_order=0;
				
				$tbl.find( 'tr[id^="tag-"]' ).each(function(i,elem) {
					do_send = true;
					var $self = $(this), id = $(elem).attr('id').replace(/[^0-9]/g,'');
					term_order[id] = start_order+i;
					
					if ( i % 2 ) $self.removeClass('alternate');
					else  $self.addClass('alternate');
				});
				
				if ( do_send ) {
					
					$.post(ajaxurl , {
						action : 'order-terms',
						order_terms : term_order,
						_wpnonce : order_taxonomies_admin.wpnonce
					},function(response) {
						var s;
						if ( response.terms_order ) {
							for ( s in response.terms_order ) {
								$( 'tr#tag-'+s+' td.term_order span' ).text( response.terms_order[s] );
							}
						}
					
					});
				}
			}
		});
	});
	
})(jQuery)