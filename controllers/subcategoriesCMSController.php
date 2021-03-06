<?php
/**
 * This class is the Controller of the Admin Sub-Categories panel.
 *
 * @author  samuelrcosta
 * @version 1.2.0, 05/24/2018
 * @since   1.0, 01/16/2017
 */

class subcategoriesCMSController extends Controller{

    // Models instances
    private $u;
    private $c;
    private $configs;

    /**
     * Class constructor
     */
    public function __construct(){
        // Initialize instances
        $this->u = new Administrators();
        $this->c = new Categories();
        $this->configs = new Configs();
        parent::__construct();
    }

    /**
     * This function shows the Admin Sub-Categories List page.
     */
    public function index(){
        $data = array();

        if($this->u->isLogged() && $this->u->havePermission('subcats')){
            $data['title'] = 'ADM - Sub-Categorias';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $this->u->getData(1, $_SESSION['adminLogin']);
            $data['subcategoriesData'] = $this->c->getSubcategoriesList();

            $this->loadTemplateCMS('cms/subcategories/index', $data);
        }else{
            header("Location: ".BASE_URL);
            exit;
        }
    }

    /**
     * This function shows the Admin Sub-Categories register page.
     */
    public function newSubCategory(){
        $data = array();

        if($this->u->isLogged() && $this->u->havePermission('subcats')){
            //Verify if exists POST for a new register

            if(isset($_POST['name']) && !empty($_POST['name'])){
                $name = addslashes($_POST['name']);
                $id_principal = addslashes($_POST['id_principal']);
                $description = addslashes($_POST['description']);
                $image = null;

                // Check if has send a image
                if(isset($_FILES) && !empty($_FILES['image']['tmp_name'])){
                    $image = $_FILES['image'];
                }

                if((!empty($name)) && !empty($id_principal)){
                    if($this->c->register($name, $description, $image, $id_principal)){
                        $msg = urlencode('Sub-Categoria cadastrada com sucesso!');
                        header("Location: ".BASE_URL."subcategoriesCMS?notification=".$msg."&status=alert-info");
                        exit;
                    }else{
                        $data['name'] = $name;
                        $data['id_principal'] = $id_principal;
                        $data['description'] = $description;
                        $data['notice'] = '<div class="alert alert-warning">Ocorreu um erro ao cadastrar a imagem ou esta sub-categoria já está cadastrada.</div>';
                    }
                }else{
                    $data['name'] = $name;
                    $data['id_principal'] = $id_principal;
                    $data['description'] = $description;
                    $data['notice'] = '<div class="alert alert-warning">Preencha todos os campos.</div>';
                }
            }

            $data['pattern_image'] = $this->configs->getConfig('pattern_category_image');
            $data['title'] = 'ADM - Nova Sub-Categoria';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $this->u->getData(1, $_SESSION['adminLogin']);
            $data['categoryData'] = $this->c->getPrincipals();

            $this->loadTemplateCMS('cms/subcategories/newSubcategory', $data);
        }else{
            header("Location: ".BASE_URL);
            exit;
        }
    }

    /**
     * This function receive a sub-category id on Base64 (2x),
     * show the edit page with the sub-category data, receive back
     * all edited data and update the database.
     * Finally headers to index page (subcategoriesCMS/index)
     */
    public function editSubCategory($id){
        $data = array();

        $id = addslashes(base64_decode(base64_decode($id)));

        if($this->u->isLogged() && $this->u->havePermission('subcats')){

            //Verify if exists POST for edit

            if(isset($_POST['name']) && !empty($_POST['name'])){
                $name = addslashes($_POST['name']);
                $id_principal = addslashes($_POST['id_principal']);
                $description = addslashes($_POST['description']);
                $image = null;

                // Check if has send a image
                if(isset($_FILES) && !empty($_FILES['image']['tmp_name'])){
                    $image = $_FILES['image'];
                }

                if((!empty($name)) && !empty($id_principal)){
                    if($this->c->edit($id, $name, $description, $image, $id_principal)){
                        $msg = urlencode('Sub-Categoria editada com sucesso!');
                        header("Location: ".BASE_URL."subcategoriesCMS?notification=".$msg."&status=alert-info");
                        exit;
                    }else{
                        $data['subcategoryData']['name'] = $name;
                        $data['subcategoryData']['id_principal'] = $id_principal;
                        $data['subcategoryData']['description'] = $description;
                        $data['notice'] = '<div class="alert alert-warning">Ocorreu um erro ao cadastrar a imagem ou já existe uma Sub-categoria com esse mesmo nome.</div>';
                    }
                }else{
                    $data['subcategoryData']['name'] = $name;
                    $data['subcategoryData']['id_principal'] = $id_principal;
                    $data['subcategoryData']['description'] = $description;
                    $data['notice'] = '<div class="alert alert-warning">Preencha todos os campos.</div>';
                }
            }else{
                //If not, render editPage
                $data['subcategoryData'] = $this->c->getDataById($id);
            }

            $data['pattern_image'] = $this->configs->getConfig('pattern_category_image');
            $data['title'] = 'ADM - Editar Sub-Categoria';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $this->u->getData(1, $_SESSION['adminLogin']);
            $data['categoryData'] = $this->c->getPrincipals();

            $this->loadTemplateCMS('cms/subcategories/editSubcategory', $data);
        }else{
            header("Location: ".BASE_URL);
            exit;
        }
    }

    /**
     * This function receive a sub-category id on Base64 (2x),
     * execute delete function and headers to index page (subcategoriesCMS/index)
     */
    public function deleteSubCategory($id){
        $u = new Administrators();
        $c = new Categories();

        $id = addslashes(base64_decode(base64_decode($id)));

        if($u->isLogged() && $u->havePermission('subcats')){
            $c->delete($id);
            header("Location: ".BASE_URL."subcategoriesCMS");
        }else{
            header("Location: ".BASE_URL);
            exit;
        }
    }

    /**
     * This function receive a sub-category id on Base64 (2x),
     * get all subcategory data and echo in json
     */
    public function getSubcategories($id){
        $id = addslashes(base64_decode(base64_decode($id)));

        $subcategoryData = $this->c->getSubcategoriesByPrincipalId($id);

        echo json_encode($subcategoryData);
    }
}