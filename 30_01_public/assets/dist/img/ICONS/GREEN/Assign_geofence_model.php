<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Assign_geofence_model extends CI_model
{
   function __construct()
	{
		parent::__construct();
	}

	public function all_assign_geofence_data()
	{ 
		$query=$this->db->query("SELECT * FROM assign_geofencetbl WHERE status!='4'");
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}

	public function save_assign_geofence($data,$id=null)
	{
		if ($id==null) {
			
					$query = $this->db->insert('assign_geofencetbl',$data);

					if($query)
					{
						return 1;
					}
					else
					{
						return 0;
					}
		}
		else
		{
				    $this->db->where('id', $id);        
			        $query = $this->db->update('assign_geofencetbl', $data);
			                  
			        if($query) 
			        {
			            return 1;
			        }
			        else
			        {
			            return 0;
			        }   

					}
				
	}

	public function edit_assign_geofencedata($id)
    { 
      
        $query = $this->db->query("SELECT * FROM assign_geofencetbl WHERE id='".$id."' ");
        
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }       
    }

  
}
