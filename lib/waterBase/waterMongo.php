<?php
        

/**
 * waterMongo provides access to a mongo database to
 * store the publicproperties of an object in a collection
 * named for that object's class.
 *
 * @package Arcane
 * @subpackage waterBase
 * @author alisdairrankine
 */
class waterMongo {

    private $mWconnection;

    private $mWcollection;

    /**
     *
     * @param string $database Name of database to connect to.
     * @param Mongo $connection Mongo object to connect to database
     */
    public function __construct($database,$connection=null){
        if ((!$connection)||(is_a($connection,"Mongo"))) $this->mWConnection = new Mongo();
        
    }

    /**
     *
     * @param mixed $object object to save.
     * @param string $id mongo ID object for updating
     */
    public function save($object,$id=null){
        if (isset($id)){
            //--------------------------------------- insert new document
            $this->mWcollection = get_class($object);
            $insertArray=get_vars($object);
            $this->mWConnection->insert($insertArray);

        } else {
            //--------------------------------------- update existing document
            $this->mWcollection = get_class($object);
            $insertArray=get_vars($object);
            $inserArray2=array();
            $id["_id"]=$id;
            foreach($insertArray as $k=>$v){
                if ($k!="_id")$insertArray2[$k]=$v;
            }
            $this->mWcollection->update($insertArray2,$id);
        }
    }

    /**
     *
     * @param mixed $object the object to populate.
     * @param string $id the id of the object.
     * @param boolean $rawData if set to true, object will not be populated, and an array of data will be returned instead
     * @return mixed if $rawdData is true, an array of data is returned, otherwise the populated object.
     */
    public function fetch($object,$id,$rawData=false){
        $this->mWcollection=$this->mWconnection->get_class($object);
        $stick = $this->mWcollection->findOne(array("_id"=>$id));
        if (!$rawData){
            foreach ($stick as $n => $v){
                $object->$n=$v;
            }
            return $object;
        } else {
            return $stick;
        }


    }
    public function delete($object,$id){

            //--------------------------------------- update existing document
            $this->mWcollection = get_class($object);
            $id["_id"]=$id;
            $this->mWcollection->remove($id);

    }

}
?>
