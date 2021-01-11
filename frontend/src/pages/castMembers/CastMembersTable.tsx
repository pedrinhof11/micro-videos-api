import React, { useEffect, useState } from "react";
import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import { httpVideo } from "../../http";
import { dateFormatFromIso } from "../../utils";
import { CastMemberTypesEnum } from "../../types/models.d";

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
  {
    name: "type",
    label: "Tipo",
    options: {
      customBodyRender: (value) => CastMemberTypesEnum[value as any],
    },
  },
  {
    name: "created_at",
    label: "Criado em",
    options: {
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
];

const CastMembersTable = () => {
  const [castMembers, setCastMembers] = useState([]);

  const fetchData = async () => {
    const {
      data: { data },
    } = await httpVideo.get("cast-members");
    setCastMembers(data);
  };

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <MUIDataTable
      title="Membros de Elenco"
      columns={columns}
      data={castMembers}
    />
  );
};

export default CastMembersTable;
