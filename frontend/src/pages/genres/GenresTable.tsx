import { Chip } from "@material-ui/core";
import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import React, { useEffect, useState } from "react";
import GenreResource from "../../http/GenreResource";
import { Category, Genre } from "../../types/models";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";

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

  return <MUIDataTable title="Gêneros" columns={columns} data={genres} />;
};

export default GenresTable;
