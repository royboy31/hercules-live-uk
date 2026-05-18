/**
 * Shared menu data for UK site - reads from Keystatic CMS
 */
import { readCollection } from '../lib/cms';

export interface MenuItem {
  label: string;
  href: string;
  icon_url: string;
}

export interface MenuData {
  sportarten: MenuItem[];
  produkte: MenuItem[];
  themen: MenuItem[];
  directLinks: { label: string; href: string }[];
}

export async function getMenuData(): Promise<MenuData> {
  const categories = readCollection<{
    categoryKey: string;
    categoryLabel: string;
    items: Array<{ label: string; href: string; iconUrl: string }>;
  }>('menu-categories');

  const menuData: MenuData = {
    sportarten: [],
    produkte: [],
    themen: [],
    directLinks: [
      { label: 'Sportswear', href: '/collections/custom-printed-sportswear/' },
      { label: 'Scarves', href: '/collections/custom-scarves/' },
      { label: 'Headwear', href: '/collections/headwear/' },
      { label: 'Beanies', href: '/collections/custom-beanies/' },
    ],
  };

  for (const cat of categories) {
    const key = cat.categoryKey as keyof Pick<MenuData, 'sportarten' | 'produkte' | 'themen'>;
    if (key in menuData && Array.isArray(menuData[key])) {
      menuData[key] = cat.items.map(item => ({
        label: item.label,
        href: item.href,
        icon_url: item.iconUrl,
      }));
    }
  }

  return menuData;
}
