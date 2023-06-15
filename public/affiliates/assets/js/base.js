/* Main Navigation Toggle */
$("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
$('.delete-affiliate').on('click', function(e) {
	var test = confirm('Are you sure you want to delete this affiliate');
	console.log(test);
	if(test === false) {
		return false;
	}
});