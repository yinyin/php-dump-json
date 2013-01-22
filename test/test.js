
module("scalar passing");

asyncTest("special-char", function() {
	expect(3);

	$.post("test.php", {"unit": "special-char"}, function(data) {
		equal(data.str1, "abc", "plain character");
		equal(data.str2, "\\'\"&\n\r<>\t", "escaped characters");
		equal(data.str3, "\ta\nb", "new line characters");
	}, "json").always(function() {
		start();
	});
});
