import React, { useEffect, useState } from "react";
import { Chip } from "@material-ui/core";
import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import { dateFormatFromIso } from "../../utils";
import { Category, Genre } from "../../types/models";
import GenreResource from "../../http/GenreResource";

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
  {
    name: "categories",
    label: "Categorias",
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
    options: {
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
];

const GenresTable = () => {
  const [genres, setGenres] = useState<Genre[]>([]);

  const fetchData = async () => {
    const {
      data: { data },
    } = await GenreResource.list();
    setGenres(data);
  };

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <MUIDataTable title="Membros do Elenco" columns={columns} data={genres} />
  );
};

export default GenresTable;
