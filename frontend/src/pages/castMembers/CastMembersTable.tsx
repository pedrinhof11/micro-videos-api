import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import React, { useEffect, useState } from "react";
import { httpVideo } from "../../http";
import { CastMemberTypesEnum } from "../../types/models.d";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";

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
  const isMountedRef = useIsMountedRef();

  useEffect(() => {
    (async () => {
      const { data } = await httpVideo.get("cast-members");
      if (isMountedRef.current) {
        setCastMembers(data.data);
      }
    })();
  }, [isMountedRef]);

  return (
    <MUIDataTable
      title="Membros de Elenco"
      columns={columns}
      data={castMembers}
    />
  );
};

export default CastMembersTable;
