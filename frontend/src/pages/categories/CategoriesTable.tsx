import { Chip } from "@material-ui/core";
import { MUIDataTableColumnDef } from "mui-datatables";
import * as React from "react";
import { useEffect, useState } from "react";
import BaseTable from "../../components/Table/BaseTable";
import CategoryResource from "../../http/CategoryResource";
import { Category } from "../../types/models";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
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
      customBodyRender: (value) => {
        return <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>;
      },
    },
  },
];

const CategoriesTable = () => {
  const [categories, setCategories] = useState<Category[]>([]);
  const isMountedRef = useIsMountedRef();

  useEffect(() => {
    (async () => {
      const { data } = await CategoryResource.list();
      if (isMountedRef.current) {
        setCategories(data.data);
      }
    })();
  }, [isMountedRef]);

  return <BaseTable title="Categorias" columns={columns} data={categories} />;
};

export default CategoriesTable;
