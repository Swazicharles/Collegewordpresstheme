import $ from 'jquery';
class Like {
    constructor(){
        this.events();
    }

    events(){
     $(".like-box").on("click",this.ourClickDispatcher.bind(this));
    }

    ourClickDispatcher(e){

        var currentLikedBox = $(e.target).closest(".like-box");

        if(currentLikedBox.attr('data-exists')=='yes'){
            this.deleteLike(currentLikedBox);
        }else{
            this.createLike(currentLikedBox);
        }
        

    }

    createLike(currentLikedBox){
        $.ajax({
            beforeSend: (xhr) =>{
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
            url:universityData.root_url+'/wp-json/university/v1/manageLike',
            type: 'POST',
            data:{
                'professor_Id':currentLikedBox.data('professor')
            },
            success:(response)=>{
                currentLikedBox.attr('data-exists','yes');
                var likeCount = parseInt(currentLikedBox.find(".like-count").html(),10);
                likeCount++;
                currentLikedBox.find(".like-count").html(likeCount);
                currentLikedBox.attr("data-like", response);
                console.log(response)
            },
            error:(response)=>{
                console.log(response)
            }

        });
    }
    deleteLike(currentLikedBox){
        $.ajax({
            beforeSend: (xhr) =>{
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
            url:universityData.root_url+'/wp-json/university/v1/manageLike',
            data:{'like': currentLikedBox.attr('data-like')},
            type: 'DELETE',
            success:(response)=>{
                currentLikedBox.attr('data-exists','no');
                var likeCount = parseInt(currentLikedBox.find(".like-count").html(),10);
                likeCount--;
                currentLikedBox.find(".like-count").html(likeCount);
                currentLikedBox.attr("data-like", '');

                console.log(response)
            },
            error:(response)=>{
                console.log(response)
            }

        })
    }

}

export default Like;