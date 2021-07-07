var myVar;
function myLoad() 
{
    myVar = setTimeout(showPage, 1000); // aumentei o tempo para ilustração
}
function showPage() 
{
   document.getElementById("myDiv").style.display = "block";
}

function conteudo(url)
{
    myLoad();
    document.getElementById("loader").style.display = "block";
    var settings = 
      {
          "async": true,
          "crossDomain": true,
          "url": url,
          "method": "GET",
          "headers": {
              "Content-Type": "application/x-www-form-urlencoded"
          }

      };    
    $.ajax(settings).done(function (response) {
      document.getElementById("loader").style.display = "none";
      $('#conteudo').html(response);          
    });
} 

function create(url)
{
    conteudo(url); 
}

function store(url,method)
{
   var formData;
   var forms = $("#formUsuario");
     
    for (var i = 0; i < forms.length; i++) 
    {
        formData = new FormData(forms[i]);       
    }
    $.ajax({
         url: url,
         type: method,
         data: formData,
         async: false,
         cache: false,
         contentType: false,
         enctype: 'multipart/form-data',
         processData: false,
         success: function (response) {
             alert(response.mensage);
             conteudo(response.url); 
         }
         
    });
}



function save(url)
{
   store(url,'POST');
}

function edit(url)
{
    create(url);
}

function update(url)
{
    store(url,'PUT');
}

function apagar(url)
{
    store(url,'DELETE');
}

function buscar(url)
{
    var settings =
        {
            "async": true,
            "crossDomain": true,
            "url": url,
            "method": 'POST',
            "headers": {
                "Content-Type": "application/x-www-form-urlencoded",                
            },
            "data" : $('#formUsuario').serialize()
        };
        $.ajax(settings).done(function (response) {
          
            $('#conteudo').html(response);        
        });
}


