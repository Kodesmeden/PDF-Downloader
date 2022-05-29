jQuery( document ).ready( function( $ ) {
	
	const params = new Proxy( new URLSearchParams( window.location.search ), {
		get: ( searchParams, prop ) => searchParams.get( prop ),
	} );
	
	let page = params.page;
	
	$( 'a.pdf-download' ).on( 'click', function( e ) {
		e.preventDefault();
		
		var $this = $(this);
		var target = $this.data( 'file' );
		
		window.location.href = '?page=' + page + '&download=' + target;
	} );
	
} );