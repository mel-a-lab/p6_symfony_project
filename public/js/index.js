$(document).ready(function() {

    const addVideoLink = document.createElement('a')
    addVideoLink.classList.add('add_video_list')
    addVideoLink.innerText='+ Ajouter vidéo(s)'
    addVideoLink.dataset.collectionHolderClass='videos';
    const removeVideoLink = document.createElement('a')
    removeVideoLink.classList.add('remove_video_list')
    removeVideoLink.innerText='- Retirer dernière vidéo'
    removeVideoLink.dataset.collectionHolderClass='videos';
    
    const newLinkLi = document.createElement('li').append(addVideoLink);
    const collectionHolder = document.querySelector('ul.videos')
    collectionHolder.appendChild(addVideoLink);
    
    const addFormToCollection = (e) => {
       const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    
       const item = document.createElement('li');
    
       item.classList.add('add_video');
    
       item.innerHTML = collectionHolder
           .dataset
           .prototype
           .replace(
               /__name__/g,
               collectionHolder.dataset.index
           );
    
       collectionHolder.appendChild(item);
    
       collectionHolder.dataset.index++;
    
       collectionHolder.appendChild(removeVideoLink);
    }
    
    const removeFormToCollection = (e) => {
       if($('.videos').children().last().prev().attr('class') == 'add_video') {
          $('.videos').children().last().prev().remove();
       }
    
       if($('.videos').children().last().prev().attr('class') != 'add_video') {
          $('.remove_video_list').remove();
       }
    }
    
    addVideoLink.addEventListener("click", addFormToCollection);
    removeVideoLink.addEventListener("click", removeFormToCollection);
    
    });
    
    
    
    
    
    
    
    
    
    