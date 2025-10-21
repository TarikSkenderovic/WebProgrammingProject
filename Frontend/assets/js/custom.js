$(document).ready(function() {


  $("main#spapp > section").height($(document).height() - 60);

  
  var app = $.spapp({
    defaultView: "#dashboard",     
    templateDir: "./views/",        
    pageNotFound: "error_404"      
  });


  app.route({ view: 'dashboard',     load: 'dashboard.html' });
  app.route({ view: 'courses',       load: 'courses.html' });
  app.route({ view: 'course-detail', load: 'course-detail.html' });
  app.route({ view: 'profile',       load: 'profile.html' });
  app.route({ view: 'login',         load: 'login.html' });
  app.route({ view: 'register',      load: 'register.html' });

  app.run();

});