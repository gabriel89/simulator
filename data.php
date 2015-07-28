<div id="dom-target" style="display: none;">
    <?php 
    $output=$file = file_get_contents('./data.txt', true);
	echo htmlspecialchars($output); 
    ?>
</div>
<div><a href="#" id="loadTreeData">Click here to load data!</a> 
<script>
	document.getElementById("loadTreeData").onclick = function () {
	    var div = document.getElementById("dom-target");
	    var myData = div.textContent;
	    console.log(myData);
	    };
</script>