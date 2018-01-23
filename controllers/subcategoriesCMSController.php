<?php
/**
 * This class is the Controller of the Admin Sub-Categories panel.
 *
 * @author  samuelrcosta
 * @version 1.0.0, 01/16/2017
 * @since   1.0, 01/16/2017
 */

class subcategoriesCMSController extends Controller{

    /**
     * Class constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * This function shows the Admin Sub-Categories List page.
     */
    public function index(){
        $u = new Administrators();
        $c = new Categories();
        $data = array();

        if($u->isLogged()){
            $data['title'] = 'ADM - Sub-Categorias';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $u->getData(1, $_SESSION['adminLogin']);
            $data['subcategoriesData'] = $c->getList();

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
        $u = new Administrators();
        $c = new Categories();
        $data = array();

        if($u->isLogged()){
            //Verify if exists POST for a new register

            if(isset($_POST['name']) && !empty($_POST['name'])){
                $name = addslashes($_POST['name']);
                $id_principal = addslashes($_POST['id_principal']);
                if(isset($_POST['presential'])){
                    $presential = 1;
                }else{
                    $presential = 0;
                }

                if((!empty($name)) && !empty($id_principal)){
                    $principalCategory = array_reverse($c->getCategoryTree($id_principal));
                    $for_user = $data['subcategoryData'] = $principalCategory['0']['for_user'];
                    if($c->register($name, $presential, $for_user, $id_principal)){
                        $msg = urlencode('Sub-Categoria cadastrada com sucesso!');
                        header("Location: ".BASE_URL."subcategoriesCMS?notification=".$msg."&status=alert-info");
                        exit;
                    }else{
                        $data['name'] = $name;
                        $data['id_principal'] = $id_principal;
                        $data['presential'] = $presential;
                        $data['notice'] = '<div class="alert alert-warning">Essa sub-categoria já está cadastrada.</div>';
                    }
                }else{
                    $data['name'] = $name;
                    $data['id_principal'] = $id_principal;
                    $data['presential'] = $presential;
                    $data['notice'] = '<div class="alert alert-warning">Preencha todos os campos.</div>';
                }
            }

            $data['title'] = 'ADM - Nova Sub-Categoria';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $u->getData(1, $_SESSION['adminLogin']);
            $data['categoryData'] = $c->getList();

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
        $u = new Administrators();
        $c = new Categories();
        $data = array();

        $id = addslashes(base64_decode(base64_decode($id)));

        if($u->isLogged()){

            //Verify if exists POST for edit

            if(isset($_POST['name']) && !empty($_POST['name'])){
                $name = addslashes($_POST['name']);
                $id_principal = addslashes($_POST['id_principal']);
                if(isset($_POST['presential'])){
                    $presential = 1;
                }else{
                    $presential = 0;
                }

                if((!empty($name)) && !empty($id_principal)){
                    $principalCategory = array_reverse($c->getCategoryTree($id_principal));
                    $for_user = $data['subcategoryData'] = $principalCategory['0']['for_user'];
                    if($c->edit($id, $name, $presential, $for_user, $id_principal)){
                        $msg = urlencode('Sub-Categoria editada com sucesso!');
                        header("Location: ".BASE_URL."subcategoriesCMS?notification=".$msg."&status=alert-info");
                        exit;
                    }else{
                        $data['subcategoryData']['name'] = $name;
                        $data['subcategoryData']['id_principal'] = $id_principal;
                        $data['subcategoryData']['presential'] = $presential;
                        $data['notice'] = '<div class="alert alert-warning">Já existe Sub-categoria com esse mesmo nome.</div>';
                    }
                }else{
                    $data['subcategoryData']['name'] = $name;
                    $data['subcategoryData']['id_principal'] = $id_principal;
                    $data['subcategoryData']['presential'] = $presential;
                    $data['notice'] = '<div class="alert alert-warning">Preencha todos os campos.</div>';
                }
            }else{
                //If not, render editPage
                $categoryList = array_reverse($c->getCategoryTree($id));
                $data['subcategoryData'] = $categoryList['0'];
            }

            $data['title'] = 'ADM - Editar Sub-Categoria';
            $data['link'] = 'subcategoriesCMS/index';
            $data['userData'] = $u->getData(1, $_SESSION['adminLogin']);
            $data['categoryData'] = $c->getList();

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

        if($u->isLogged()){
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
        $c = new Categories();

        $id = addslashes(base64_decode(base64_decode($id)));

        $subcategoryData = array();

        $data = $c->getList();
        foreach ($data as $category){
            if($category['id'] == $id){
                foreach ($category['subs'] as $subcategory){
                    $subcategoryData[] = $subcategory;
                }
            }
        }

        echo json_encode($subcategoryData);
    }
}