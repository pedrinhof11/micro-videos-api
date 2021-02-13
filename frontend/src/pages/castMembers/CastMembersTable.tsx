import { IconButton, MuiThemeProvider } from "@material-ui/core";
import { Edit as EditIcon } from "@material-ui/icons";
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  TableColumn,
} from "../../components/Table/BaseTable";
import { httpVideo } from "../../http";
import { CastMemberTypesEnum } from "../../types/models.d";
import { dateFormatFromIso } from "../../utils";
import useIsMountedRef from "../../hooks/useIsMountedRef";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "25%" },
  { name: "name", label: "Nome", width: "40%" },
  {
    name: "type",
    label: "Tipo",
    width: "10%",
    options: {
      customBodyRender: (value: any) => CastMemberTypesEnum[value as any],
    },
  },
  {
    name: "created_at",
    label: "Criado em",
    width: "10%",
    options: {
      customBodyRender: (value: string) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
  {
    name: "actions",
    label: "Ações",
    width: "10%",
    options: {
      customBodyRender: (value: any, tableMeta: { rowData: any[] }) => {
        return (
          <IconButton
            title="editar Membro dde Elenco"
            color="secondary"
            component={Link}
            to={`/cast-members/${tableMeta.rowData[0]}/edit`}
          >
            <EditIcon fontSize="inherit" />
          </IconButton>
        );
      },
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
    <MuiThemeProvider theme={makeActionThemes(columns.length - 1)}>
      <BaseTable
        title="Membros de Elenco"
        columns={columns}
        data={castMembers}
      />
    </MuiThemeProvider>
  );
};

export default CastMembersTable;
