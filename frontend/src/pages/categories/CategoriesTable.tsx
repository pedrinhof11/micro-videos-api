import { Chip } from "@material-ui/core";
import { useSnackbar } from "notistack";
import * as React from "react";
import { useEffect, useState } from "react";
import BaseTable, { TableColumn } from "../../components/Table/BaseTable";
import CategoryResource from "../../http/CategoryResource";
import { Category } from "../../types/models";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "33%" },
  { name: "name", label: "Nome", width: "40%" },
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
      customBodyRender: (value) => {
        return <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>;
      },
    },
  },
  {
    name: "actions",
    label: " ",
    width: "13%",
  },
];

const CategoriesTable = () => {
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState<boolean>(false);

  useEffect(() => {
    (async () => {
      setLoading(true);
      try {
        const { data } = await CategoryResource.list();
        if (isMountedRef.current) {
          setCategories(data.data);
        }
      } catch (error) {
        snackbar.enqueueSnackbar("Não possivel carregar as informações", {
          variant: "error",
        });
      } finally {
        setLoading(false);
      }
    })();
  }, [isMountedRef, snackbar]);

  return (
    <BaseTable
      title="Categorias"
      columns={columns}
      data={categories}
      loading={loading}
    />
  );
};

export default CategoriesTable;
