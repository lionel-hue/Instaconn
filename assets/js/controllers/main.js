import {articles_post, articles_get} from "./accueil/articles.js"
import { setFormulaire } from "./accueil/setFormulaire.js"; 
import { modifier_profile, set_profile } from "./profile/profile.js";
    
let params = new URLSearchParams(window.location.search)
let page = params.get("page")
if (!page) page = "accueil"


// After fetching user data (e.g., in your main app.js)
fetch('/api/user.php')

.then(res => res.json())

.then(data => {
if (data === "error") {
  window.location.replace("/views/clients/seconnecter.html"); // Hard redirect (no history entry)
}
})


;(async function() 
{
    const pg = await fetch('/views/clients/' + page + '.html')
    const res = await pg.text()
    document.getElementById('mainContent').innerHTML = res
    
    //controller    
    switch(page)
    {
        case "accueil":
            //GET /api/accueil/articles.php endpoint
            await setFormulaire()
            await articles_get()

            // POST  /api/accueil/envoyer_article.php endpoint
            await articles_post()

            break
        
        case "profile":
          //GET /api/profile/user.php
          await set_profile()

          //POST /api/profile/profile.php
          await modifier_profile()
            break
            
        case "amis":
            break
    }
})()