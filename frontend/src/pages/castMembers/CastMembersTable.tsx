import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import React, { useEffect, useState } from "react";
import { httpVideo } from "../../http";
import { dateFormatFromIso } from "../../utils";

enum CastMemberTypesEnum {
  Diretor = 1,
  Ator = 2,
}

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
  {
    name: "type",
    label: "Tipo",
    options: {
      customBodyRender: (value) => CastMemberTypesEnum[value]
    }
  },
  { 
    name: "created_at",
    label: "Criado em",
    options: {
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, 'dd/MM/yyyy')}</span>
      )
    } 
  }
];

const CastMembersTable = () => {

  const [castMembers, setCastMembers] = useState([]);
  
  const fetchData = async () => {
    const {data: { data }} = await httpVideo.get("cast-members");
    setCastMembers(data);
  }

  useEffect(() => {
    fetchData()
  }, []);


  return (
    <MUIDataTable
    title="Membros de Elenco"
    columns={columns}
    data={castMembers}
  />
  );
}

export default CastMembersTable
