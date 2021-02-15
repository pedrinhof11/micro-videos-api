import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

export const dateFormatFromIso = (date: string, formatStr: string) =>
  format(parseISO(date), formatStr);

