<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 24.03.14
 * Time: 19:31
 */

class RessBereich {
    private $id;
    private $name;
    private $ress = array();

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setRess($ress)
    {
        $this->ress[] = $ress;
    }

    /**
     * Erzeugt den 'ress_id_block'-Template
     */
    public function ressToTemplate()
    {
        global $template, $phpbb_root_path, $user;

        //Durchläuft alle Ress in dem Bereich]
        foreach($this->ress AS $value)
        {
            $template->assign_block_vars('ress_'. $this->id .'_block', array(
                'RESS_NAME'		=> $value->getName(),
                'RESS_URL'		=> $phpbb_root_path.$value->getUrl(),
                'BEREICH_NAME'	=> $this->name,
                'MENGE'			=> $value->getMenge(),
                'MAX_MENGE'     => ($value->getId() == 1)? MAX_CREDITS : $user->data['user_rsp_lagergroesse'],
            ));
        }
    }

    /**
     * Erzeugt den 'ress_block'-Template
     */
    public function ressToListe()
    {
        global $template;

        $template->assign_block_vars('ress_block', array(
            'ID'		=> -1,
            'NAME'		=> $this->name,
        ));
        //Durchläuft alle Ress in dem Bereich
        foreach($this->ress AS $value)
        {
            $template->assign_block_vars('ress_block', array(
                'ID'		=> $value->getId(),
                'NAME'		=> '&nbsp;&nbsp;-' . $value->getName(),
            ));
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRess()
    {
        return $this->ress;
    }

} 