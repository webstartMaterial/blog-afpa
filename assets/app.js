/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// loads the jquery package from node_modules
import $ from 'jquery';

// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
//import greet from '../greet';

$(document).ready(function() {

    console.log("hello World");

    // $(window).keydown(function(event){
    //     if(event.keyCode == 13) {
    //       event.preventDefault();
    //       return false;
    //     }
    //   });

    $("#search-content").on('keydown', function(e) {
        if(e.key === "Enter" || e.keyCode === 13) {
            e.preventDefault();// j'empêche le formulaire de se soumettre

            $.ajax({
                url:'/search',
                data: {
                    "search": $("#search-content").val()
                },
                dataType: 'json',
                success: function (data) // obj json => un array d'objet
                {
                    let contentHtml = "";

                    contentHtml += "<h1 class='mb-5'> Nos articles liés à la recherche ' " + $("#search-content").val() + " ' </h1>";

                    for(let i = 0; i < data.length; i++) {
                        contentHtml += "<div class='article d-flex my-3'>"+
                                "<a class='d-flex text-decoration-none text-reset' href='/article/" + data[i].id + "'>"+
                                    "<img src='/images/articles/" + data[i].picture + "'>"+
                                    "<div class='d-flex flex-column ps-2'>"+   
                                        "<h2>" + data[i].title + " - " + data[i].date + " </h2>"+
                                        "<p>" + data[i].description  + " </p>"+
                                    "</div>"+
                                "</a>"+
                            "</div>";
                    }
                    
                    // dans la classe inner-content
                    // met le contenu
                    $(".inner-content").html(contentHtml);
                    

                }
            });
        }
    });

    //
    // Gérer la suppression d'un article en asynchrone
    //
    $(".delete-button").on('click', function() {


        // id article
        let id = $(this).attr("id");
        console.log(id);

        let article = $(this).closest('.article'); // permet de récupérer l'article (le block html)
        // à faire disparaitre après suppression

        $.ajax({
            url:'/delete',
            method: 'POST',
            data: {
                "id": id
            },
            dataType: 'json',
            success: function (data) // obj json => un array d'objet
            {
                article.remove(); // je supprime le block html de la page
            }
        });

    });

});