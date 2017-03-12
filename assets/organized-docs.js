// Add jump-links to each h2#docs- heading on the page
var nodes= document.body.getElementsByTagName('h2'),
L= nodes.length, ids= [], titles= [], temp;
for ( var i = 0; i < L; i++ ) {
	if ( nodes[i].id.indexOf( 'docs-' )== 0 ) {
		var newItem = document.createElement( 'li' );	// Create a <li> node
		// if it's an subheading, add a class selector
		if ( nodes[i].id.indexOf( 'docs-subheading-' )== 0 ) {
			newItem.className = 'docs-subheading';
		}
		var a = document.createElement( 'a' );		// create a link
		var linkText = document.createTextNode( nodes[i].textContent );
		a.appendChild( linkText );
		a.href = '#' + nodes[i].id;
		newItem.appendChild(a);	// Append the a link to <li>
		var list = document.getElementById( 'odocs-only-one' );// Get the sidebar <ul> element
		list.appendChild( newItem ); // Add the link to the sidebar <ul>

	}
}
