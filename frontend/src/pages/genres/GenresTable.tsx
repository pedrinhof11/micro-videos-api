import { Chip, IconButton, MuiThemeProvider } from "@material-ui/core";
import EditIcon from "@material-ui/icons/Edit";
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  TableColumn,
} from "../../components/Table/BaseTable";
import GenreResource from "../../http/GenreResource";
import { Category, Genre } from "../../types/models";
import { dateFormatFromIso } from "../../utils";
import useIsMountedRef from "../../hooks/useIsMountedRef";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "33%" },
  { name: "name", label: "Nome", width: "33%" },
  {
    name: "categories",
    label: "Categorias",
    width: "33%",
    options: {
      customBodyRender: (categories) => {
        return (categories as any)
          .map((value: Category) => value.name)
          .join(", ");
      },
    },
  },
  {
    name: "is_active",
    label: "Ativo?",
    width: "4%",
    options: {
      customBodyRender: (value) => {
        return (value as any) ? (
          <Chip label="Sim" color="primary" />
        ) : (
          <Chip label="Não" color="secondary" />
        );
      },
    },
  },
  {
    name: "created_at",
    label: "Criado em",
    width: "10%",
    options: {
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
  {
    name: "actions",
    label: "Ações",
    width: "13%",
    options: {
      customBodyRender: (value, tableMeta) => {
        return (
          <IconButton
            title="editar Gênero"
            color="secondary"
            component={Link}
            to={`/genres/${tableMeta.rowData[0]}/edit`}
          >
            <EditIcon fontSize="inherit" />
          </IconButton>
        );
      },
    },
  },
];

const GenresTable = () => {
  const [genres, setGenres] = useState<Genre[]>([]);
  const isMountedRef = useIsMountedRef();

  useEffect(() => {
    (async () => {
      const {
        data: { data },
      } = await GenreResource.list();
      if (isMountedRef.current) {
        setGenres(data);
      }
    })();
  }, [isMountedRef]);

  return (
    <MuiThemeProvider theme={makeActionThemes(columns.length - 1)}>
      <BaseTable title="Gêneros" columns={columns} data={genres} />
    </MuiThemeProvider>
  );
};

export default GenresTable;
