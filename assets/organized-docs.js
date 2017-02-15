// Add jump-links to each h2#docs- heading on the page

var nodes= document.body.getElementsByTagName('h2'),
L= nodes.length, ids= [], titles= [], temp;
for ( var i = 0; i < L; i++ ) {
	if ( nodes[i].id.indexOf( 'docs-' )== 0 ) {
		var newItem = document.createElement( 'li' );	// Create a <li> node
		var a = document.createElement( 'a' );		// create a link
		var linkText = document.createTextNode( nodes[i].textContent );
		a.appendChild( linkText );
		a.href = '#' + nodes[i].id;
		newItem.appendChild(a);	// Append the a to <li>
		var list = document.getElementById( 'odocs-only-one' );// Get the <ul> element
		list.appendChild( newItem );

	}
}
