/* ====================================== */
/* ========= LOAD JSON DATA============== */
/* ====================================== */
/* ========== POPULATE PAGE ============= */
/* ====================================== */

// when HTML document is ready (loaded) perform the following function:
$(document).ready(function() {

    // variable to store all data from JSON with global scope
    var dataFields = new Array();

    $.ajax({
        type : 'GET',
        dataType : 'json',
        async: false,
        url: 'db_Data.json',
        success : function(data) {
            for (var i in data) {
                // store the data to the global variable
                dataFields.push(data[i]);

                // THIS IS FOR THE ORIGINAL AUTHOR ID BUTTONS BEFORE BUBBLE CHART //

                // create the various buttons labelled with the ID values and store as $poet variable
                /* $poet = $("<section class='main'><p>" + data[i].ID + "</p></section>");
                // add a data selection to our new DOM element, $poet, to retrieve details according to click on ID
                $poet.data('FIRSTNAME', data[i].FIRSTNAME);
                $poet.data('SURNAME', data[i].SURNAME);
                $poet.data('DATE', data[i].DATE);
                $poet.data('BOOKS', data[i].BOOKS);
                $poet.data('DESCRIPTION', data[i].DESCRIPTION);

                // when element ID is clicked on page:
                $poet.on("click", function() {
                    // save all the data selection to variables to use with jquery append function below
                    var firstName = $(this).data('FIRSTNAME');
                    var surName = $(this).data('SURNAME');
                    var date = $(this).data('DATE');
                    var books = $(this).data('BOOKS');
                    var bio = $(this).data('DESCRIPTION');

                    // empty main section of all elements to make space for new data
                    $("#mainBlock").empty();
                    // create new block of data with relevent elements
                    $("#mainBlock").append("<section class='meta'><p>Name:  <p class='data'>" + firstName + " " + surName + "</p></p><p>Dates Active:  <p class='data'>" + date + "</p></p><p>Books:<br><p class='data'>" + books + "</p></p><p>Biography:<br><p class='data'>" + bio + "</p></p></section>");
                    // create new block for manuscripts
                    $("#mainBlock").append("<br>" + "<div><img class='zoom' src='manuscripts/BXB-1-1-ADC-1-1thumb.png' data-zoom-image='manuscripts/BXB-1-1-ADC-1-1.png'/></div>");
                    $("#mainBlock").append("<br>" + "<div><img class='zoom' src='manuscripts/BXB-1-1-ADC-1-2-1thumb.png' data-zoom-image='manuscripts/BXB-1-1-ADC-1-2-1.png'/>" + " " + "<img class='zoom' src='manuscripts/BXB-1-1-ADC-1-2-2thumb.png' data-zoom-image='manuscripts/BXB-1-1-ADC-1-2-2.png'/>" + " " + "<img class='zoom' src='manuscripts/BXB-1-1-ADC-1-2-3thumb.png' data-zoom-image='manuscripts/BXB-1-1-ADC-1-2-3.png'/>" + " " + "<img class='zoom' src='manuscripts/BXB-1-1-ADC-1-2-4thumb.png' data-zoom-image='manuscripts/BXB-1-1-ADC-1-2-4.png'/></div>" + "<br>");
                    // magnifying function for manuscripts
                    $(".zoom").elevateZoom({
                        zoomType: "inner",
                        lensShape: "square",
                        lensSize: 300
                    });
                });
                // add the ID block for this iteration to main section of webpage, move to next
                $("#mainBlock").append($poet); */
            }
            // call function to create author ID based bubble chart (below) //
            d3bubbles();
        }
    });

    /* ============================================== */
    /* =============== D3 BUBBLE CHART ============== */
    /* ============================================== */

    function d3bubbles(dataset) {

        var diameter = 960;

        // create new pack layout set to variable 'bubble'
        var bubble = d3.layout.pack()
                              // no sorting, size allocated 960 x 960 with padding 1.5px
                              .sort(null)
                              .size([diameter, diameter])

        // select #mainBlock element and append an svg canvas to 'draw' the circles onto
        var canvas = d3.select("#mainBlock").append("svg")
                                   .attr("width", diameter)
                                   .attr("height", diameter)
                                   .append("g")

        // specially created JSON file for bubble chart
        d3.json("cleanJson.json", function (data) {
            // run pack layout specified above returning array of nodes associated with 'data' from JSON file
            // outputs array with computed position of all nodes as 'nodes' and populates some data for each node:
            // depth, starting at 0 for root, x coordinate of node, y coordinate of node, radius r of node
            var nodes = bubble.nodes(data);
        
            var node = canvas.selectAll(".node")
                             .data(nodes)
                             .enter()
                             // standard html element to display svg
                             .append("g")
                             // give nodes a class name for referencing
                             .attr("class", "node")
                             .attr("transform", function (d) {
                                 return "translate(" + d.x + ", " + d.y + ")";
                             });

            node.append("circle")
                // radius from data
                .attr("r", function (d) {
                    return d.r;
                })
                // colour circles according to associated number of manuscripts
                .attr("fill", function (d) {
                    if ((d.value <= 24) && (d.value > 18)) {
                        // dark blue
                        return "#292f63";
                    }
                    // don't display root node, i.e. make same colour as background
                    else if (d.value === 255) {
                        // grey
                        return "#4a4c41";
                    }
                    else if ((d.value <= 18) && (d.value > 12)) {
                        // light blue
                        return "#69c3ff";
                    }
                    else if ((d.value <= 12) && (d.value > 6)) {
                        // dark green
                        return "#2a5b25";
                    }
                    else {
                        // light green
                        return "#7b993f";
                    }
                })
                // set stroke for circles but only on leaf nodes
                .attr("stroke", function (d) {
                    return d.children ? "": "#f4efdd";
                })
                .attr("stroke-width", 5);
            // add the ID text value to identify the nodes, set to variable $click for later reference
            $click = node.append("text")
                .style("text-anchor", "middle")
                .style("font-family", "'Raleway', sans-serif")
                .style("font-weight", "bold")
                .style("fill", "#f4efdd")
                .attr("dy", ".3em")
                .text(function (d) {
                    // if the node has children then no text, i.e. only display text on leaf nodes
                    return d.children ? "" : d.name;
                })
                // when user clicks on an author ID then do:
                $click.on("click", function (d) {

                    // find the element from 'db_Data.json' where ID matches 'name' in 'cleanJson.json'
                    var clickData = dataFields.filter (function (f) {
                        return (f.ID === d.name);
                    });
                    // build the DOM elements for that data
                    for (var i in data.children) {
                        // save all the data selection to variables to use with jquery append function below
                        // makes an extra step but easier to see what's going on here
                        var firstName = clickData[i].FIRSTNAME;
                        var surName = clickData[i].SURNAME;
                        var date = clickData[i].DATE;
                        var books = clickData[i].BOOKS;
                        var bio = clickData[i].DESCRIPTION;
                        var links = clickData[i].IMAGEPATHS;

                        // empty main section of all elements to make space for new data
                        $("#mainBlock").empty();
                        // shift down to avoid header
                        $("#mainBlock").css("margin-top", "-70px");
                        // create new block of data with relevent metadata
                        $("#mainBlock").append("<section class='meta'><p>Name:  <p class='data'>" + firstName + " " + surName + "</p></p><p>Dates Active:  <p class='data'>" + date + "</p></p><p>Books:<br><p class='data'>" + books + "</p></p><p>Biography:<br><p class='data'>" + bio + "</p></p></section>");
                        // append 'back' button to return to ID bubble chart selection (index.html)
                        $("#mainBlock").append("<a href='index.html'><img class='home' src='chevron.png'></a>");
                        // create new block for manuscripts
                        var newElement ="<div class='images'>";
                        for(var key in links){

                            // iterate and add each JPEG according to filepath designated in links, setup according to 'fancybox' requirements
                            newElement += "<a class='grouped_elements' data-fancybox-group='gallery' href='images/" + links[key] + "'><img src='thumbs/" + links[key] + "'/></a>";
                        };
                        // close the div for 'fancybox'
                        newElement += "</div>";
                        $("#imageContainer").append(newElement)
                        .css("display", "inline");

                        // initialise fancybox plugin on all elements with class 'fancybox' and set attributes
                        $("a.grouped_elements").fancybox( {
                            'transitionIn': 'elastic',
                            'transitionOut': 'elastic',
                            'speedIn': '600',
                            'speedOut': '200',
                            'overlayShow': true
                        });
                        
                        // THIS WAS BEFORE FANCYBOX WAS IMPLEMENTED, FOR MAGNIFYING THE DOCUMENTS
                        // COULD PERHAPS BE USED ALONG WITH FANCYBOX AS DIFFICULT TO READ AT THE MOMENT
                        // magnifying function for manuscripts
                        /* $(".zoom").elevateZoom({
                            zoomType: "inner",
                            lensShape: "square",
                            lensSize: 300
                        }); */

                        //initiate the plugin and pass the id of the div containing gallery images
                        /* $(".zoom").elevateZoom( {
                            gallery:'gallery_01',
                            cursor: 'pointer',
                            galleryActiveClass: 'active',
                            imageCrossfade: true,
                            loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'
                        }); */
                    };
                });

            // tooltip, when hover over a circle, displays number of associated manuscripts
            $('svg circle').tipsy( { 
                gravity: 'w',
                fade: true,
                html: true, 
                title: function() {
                    var d = this.__data__;
                    return d.children ? "" : d.value + " Manuscripts"; 
                }
            })
        });
    };

    /* ========================================== */
    /* ============ ANIMATED LOGO =============== */
    /* ========================================== */

    // on clicking any element with class 'logo' will allow to move using keyboard cursor keys (identified with numbers 37-40)
    /* $(document).keydown(function(key) {
        switch(parseInt(key.which,10)) {
            // Left arrow key pressed
            case 37:
                $('#logo').animate({left: "-=10px"}, 'fast');
                break;
            // Up Arrow Pressed
            case 38:
                $('#logo').animate({top: "-=10px"}, 'fast');
                break;
            // Right Arrow Pressed
            case 39:
                $('#logo').animate({left: "+=10px"}, 'fast');
                break;
            // Down Array Pressed
            case 40:
                $('#logo').animate({top: "+=10px"}, 'fast');
                break;
        }
    }); */
});